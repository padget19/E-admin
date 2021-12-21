<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-05-14
 * Time: 22:00
 */

namespace Eadmin\grid;

use Eadmin\component\form\FormAction;
use Eadmin\component\layout\Row;
use Eadmin\form\Form;
use think\db\Query;
use think\facade\Db;
use think\Model;
use think\model\Relation;
use think\model\relation\BelongsTo;
use think\model\relation\HasMany;
use think\model\relation\HasOne;
use think\model\relation\MorphMany;
use think\model\relation\MorphOne;
use Eadmin\form\field\Input;

class Filter
{
    //模型
    protected $model;
    //当前模型db
    protected $db;
    protected $tableFields = [];
    protected $jsonNode = '';
    protected $relationExistSql = '';
    protected $ifWhere = true;
    protected $relationLastDb = null;
    protected $relationLastMethod = '';
    protected $columnNum = 0;
    public function __construct($model)
    {
        if ($model instanceof Model) {
            $this->model = $model;
            $this->db    = $this->model->db();
        } elseif ($model instanceof Query) {
            $this->db    = $model;
            $this->model = $model->getModel();
        }
        if ($this->db) {
            $this->tableFields = $this->db->getTableFields();
        }
        $this->form = new Form([]);
        $this->form->inline()
            ->removeAttr('labelWidth')
            ->removeAttr('setAction')
            ->size('small');
        $this->form->actions(function (FormAction $action) {
            $action->submitButton()->content('搜索')->sizeSmall()->icon('el-icon-search');
            $action->resetButton()->sizeSmall();
        });
    }

    /**
     * 布局列数
     * @param $num
     */
    public function column($num){
        $this->columnNum = $num;
    }
    /**
     * 模糊查询
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function like($field, $label = '')
    {
        $field = $this->parseFilter(__FUNCTION__, $field);
        $this->form->text($field, $label)->prefixIcon('el-icon-search');
        return $this;
    }

    /**
     * json查询
     * @param string $field 字段
     * @param string $node json属性字段
     * @param string $label 标签
     * @return $this
     */
    public function json($field, $node, $label = '')
    {
        $this->jsonNode = $node;
        $field = $this->parseFilter(__FUNCTION__, $field);
        $this->form->text($field . '__json_' . $node, $label)->prefixIcon('el-icon-search');
        return $this;
    }

    /**
     * json模糊查询
     * @param string $field 字段
     * @param string $node json属性字段
     * @param string $label 标签
     * @return $this
     */
    public function jsonLike($field, $node, $label = '')
    {
        $this->jsonNode = $node;
        $field = $this->parseFilter(__FUNCTION__, $field);
        $this->form->text($field . '__json_' . $node, $label)->prefixIcon('el-icon-search');
        return $this;
    }

    /**
     * json数组模糊查询
     * @param string $field 字段
     * @param string $node json属性字段
     * @param string $label 标签
     * @return $this
     */
    public function jsonArrLike($field, $node, $label = '')
    {
        $this->jsonNode = $node;
        $field = $this->parseFilter(__FUNCTION__, $field);
        $this->form->text($field . '__json_' . $node, $label)->prefixIcon('el-icon-search');
        return $this;
    }

    /**
     * in查询
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function in($field, $label = '')
    {
        $field = $this->parseFilter(__FUNCTION__, $field);
        $this->form->text($field, $label)->prefixIcon('el-icon-search');
        return $this;
    }

    /**
     * not in查询
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function notIn($field, $label = '')
    {
        $field = $this->parseFilter(__FUNCTION__, $field);
        $this->form->text($field, $label)->prefixIcon('el-icon-search');
        return $this;
    }

    /**
     * 等于查询
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function eq($field, $label = '')
    {
        $field = $this->parseFilter(__FUNCTION__, $field);
        $this->form->text($field, $label)->prefixIcon('el-icon-search');
        return $this;
    }

    /**
     * findIn查询
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function findIn($field, $label = '')
    {
        $field = $this->parseFilter(__FUNCTION__, $field);
        $this->form->text($field, $label)->prefixIcon('el-icon-search');
        return $this;
    }

    /**
     * 不等于查询
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function neq($field, $label = '')
    {
        $field = $this->parseFilter(__FUNCTION__, $field);
        $this->form->text($field, $label)->prepend('不等于')->prefixIcon('el-icon-search');
        return $this;
    }

    /**
     * 大于等于
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function egt($field, $label = '')
    {
        $field = $this->parseFilter(__FUNCTION__, $field);
        $this->form->text($field, $label)->prepend('大于等于')->prefixIcon('el-icon-search');
        return $this;
    }

    /**
     * 大于
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function gt($field, $label = '')
    {
        $field = $this->parseFilter(__FUNCTION__, $field);
        $this->form->text($field, $label)->prepend('大于')->prefixIcon('el-icon-search');
        return $this;
    }

    /**
     * 小于等于
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function elt($field, $label = '')
    {
        $field = $this->parseFilter(__FUNCTION__, $field);
        $this->form->text($field, $label)->prepend('小于等于')->prefixIcon('el-icon-search');
        return $this;
    }

    /**
     * 小于
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function lt($field, $label = '')
    {
        $field = $this->parseFilter(__FUNCTION__, $field);
        $this->form->text($field, $label)->prepend('小于')->prefixIcon('el-icon-search');
        return $this;
    }

    /**
     * 区间查询
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function between($field, $label = '')
    {
        $field = $this->parseFilter(__FUNCTION__, $field);
        $this->form->text($field . '__between_start', $label)->placeholder("请输入开始$label");
        $this->form->text($field . '__between_end', '-')->placeholder("请输入结束$label");
        return $this;
    }

    /**
     * 日期筛选
     * @param string $field 字段
     * @param string $label 标签
     * @return \Eadmin\component\form\field\DatePicker|$this
     */
    public function date($field, $label = '')
    {
        $field = $this->parseFilter('eq', $field);
        return $this->form->date($field, $label);
    }

    /**
     * 时间筛选
     * @param string $field 字段
     * @param string $label 标签
     * @return \Eadmin\component\form\field\TimePicker|$this
     */
    public function time($field, $label = '')
    {
        $field = $this->parseFilter('eq', $field);
        return $this->form->time($field, $label);
    }

    /**
     * 日期时间筛选
     * @param string $field 字段
     * @param string $label 标签
     * @return \Eadmin\component\form\field\DatePicker
     */
    public function datetime($field, $label = '')
    {
        $field = $this->parseFilter('eq', $field);
        return $this->form->datetime($field, $label);
    }

    /**
     * 日期时间范围筛选
     * @param string $field 字段
     * @param string $label 标签
     * @return \Eadmin\component\form\field\DatePicker
     */
    public function datetimeRange($field, $label = '')
    {
        $field = $this->parseFilter('dateBetween', $field);
        return $this->form->datetimeRange($field . '__start', $field . '__end', $label);
    }

    /**
     * 级联筛选
     * @param mixed ...$field 字段1,字段2,字段3...
     * @param string $label 标签
     * @return \Eadmin\component\form\field\Cascader
     */
    public function cascader(...$field)
    {
        $cascader     = $this->form->cascader(...$field);
        $requestField = 'cascader'.md5(implode(',', $field));
        $cascader->bind($requestField, '');
        $cascader->bindAttr('modelValue', $requestField);
        $cascader->getFormItem()->prop($requestField);
        array_unshift($field, $requestField);
        array_pop($field);
        $this->parseFilter('cascader', $field);
        return $cascader;
    }

    /**
     * 日期范围筛选
     * @param string $field 字段
     * @param string $label 标签
     * @return \Eadmin\component\form\field\DatePicker
     */
    public function dateRange($field, $label = '')
    {
        $field = $this->parseFilter('dateBetween', $field);
        return $this->form->dateRange($field . '__start', $field . '__end', $label);
    }

    /**
     * 时间范围筛选
     * @param string $field 字段
     * @param string $label 标签
     * @return \Eadmin\component\form\field\TimePicker
     */
    public function timeRange($field, $label = '')
    {
        $field = $this->parseFilter('dateBetween', $field);
        return $this->form->timeRange($field . '__start', $field . '__end', $label);
    }

    /**
     * 年日期筛选
     * @param string $field 字段
     * @param string $label 标签
     * @return \Eadmin\component\form\field\DatePicker
     */
    public function year($field, $label = '')
    {
        $field = $this->parseFilter(__FUNCTION__, $field);
        return $this->form->year($field, $label);
    }

    /**
     * 月日期筛选
     * @param string $field 字段
     * @param string $label 标签
     * @return \Eadmin\component\form\field\DatePicker
     */
    public function month($field, $label = '')
    {
        $field = $this->parseFilter(__FUNCTION__, $field);
        return $this->form->month($field, $label);
    }

    /**
     * NOT区间查询
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function notBetween($field, $label = '')
    {
        $field = $this->parseFilter(__FUNCTION__, $field);
        $this->form->text($field . '__between_start', $label)->placeholder("不存在区间");
        $this->form->text($field . '__between_end', '-')->placeholder("请输入$label");
        return $this;
    }

    /**
     * 单选框
     * @param array $options 选项值
     * @return \Eadmin\component\form\field\RadioGroup
     */
    public function radio(array $options)
    {
        $item  = $this->form->popItem();
        $field = $item->attr('prop');
        $label = $item->attr('label');
        return $this->form->radio($field, $label)->options($options);
    }

    /**
     * 多选框
     * @param array $options 选项值
     * @return \Eadmin\component\form\field\CheckboxGroup
     */
    public function checkbox(array $options)
    {
        $item  = $this->form->popItem();
        $field = $item->attr('prop');
        $label = $item->attr('label');
        return $this->form->checkbox($field, $label)->options($options);
    }

    /**
     * 分组下拉框
     * @param array  $options 选项值
     * @param string $name 分组字段名
	 * @param string $optionLabel 选项名称
	 * @param string $id 选项id
     * @return \Eadmin\component\form\field\Select
     */
    public function selectGroup(array $options, $name = 'options', $optionLabel = 'label', $id = 'id')
    {
        $item  = $this->form->popItem();
        $field = $item->attr('prop');
        $label = $item->attr('label');
        return $this->form->select($field, $label)->groupOptions($options, $name, $optionLabel, $id);
    }

    /**
     * 下拉框
     * @param array $options 选项值
     * @return \Eadmin\component\form\field\Select
     */
    public function select(array $options)
    {
        $item  = $this->form->popItem();
        $field = $item->attr('prop');
        $label = $item->attr('label');
        return $this->form->select($field, $label)->options($options);
    }

    /**
     * 解析查询过滤
     * @param string $method 方法
     * @param string $field 字段
     * @return mixed
     */
    public function parseFilter($method, $field)
    {
        $requestField = $field;
        if ($this->db) {
            if (is_string($field)) {
                $field   = str_replace('.', '__', $field);
                $fields  = explode('__', $field);
                $dbField = array_pop($fields);
                $requestField = $field;
                if (count($fields) > 0) {
                    $func = function (Filter $filter) use ($dbField, $field, $method) {
                        $filter->filterField($method, $dbField, $field);
                    };
                    while (count($fields) > 1) {
                        $relation = array_pop($fields);
                        $func     = function (Filter $filter) use ($relation, $func, $dbField) {
                            $filter->relationWhere($relation, $func);
                        };
                    }
                    $relation = array_pop($fields);
                    $this->relationWhere($relation, $func);
                    return $requestField;
                }
            } elseif (is_array($field)) {
                $requestField = array_shift($field);
                $dbField      = $field;
            }
            $this->filterField($method, $dbField, $requestField);
        }
        return $requestField;
    }

    /**
     * 查询过滤
     * @param string $method 方法
     * @param string $dbField 数据库字段
     * @param string $field 请求数据字段
     * @param string $request 请求方式
     * @return $this
     */
    private function filterField($method, $dbField, $field = null, $request = 'get')
    {
        if (is_null($field)) {
            $field = $dbField;
        }

        $data = request()->$request();
        if ($method == 'between' || $method == 'notBetween') {
            $field .= '__between_start';
        }
        if ($method == 'dateBetween') {
            $field .= '__start';
        }
        if ($method == 'json' || $method == 'jsonLike' || $method == 'jsonArrLike') {
            $field .= '__json_' . $this->jsonNode;
        }
        if (is_array($dbField)) {
            $dbFields = $dbField;

        } else {
            $dbFields[] = $dbField;
        }

        $whereOr = [];

        foreach ($dbFields as $f) {
            if (isset($data[$field]) && $data[$field] !== '') {
                if (is_array($data[$field]) && $method == 'cascader') {
                    $value = array_shift($data[$field]);
                    if (is_null($value)) {
                        continue;
                    }
                    $fieldData[$field] = $value;
                    $res               = json_decode($fieldData[$field], true);

                    if (!is_null($res)) {
                        $fieldData[$field] = $res;
                    }
                    if (is_array($fieldData[$field])) {
                        $where = [];
                        foreach ($fieldData[$field] as $index => $value) {
                            $where[] = [$dbFields[$index], '=', $value];
                        }
                        $whereOr[] = $where;
                        continue;
                    }
                } else {
                    $fieldData = $data;
                }
                $this->parseRule($method, $f, $field, $fieldData);
            }
        }

        if (!empty($whereOr)) {
            $fieldData[$field] = $whereOr;
            $this->parseRule($method, $f, $field, $fieldData);
        }
        return $this;
    }

    /**
     * @param string $method 方法
     * @param string  $dbField 字段
     * @param $field
     * @param $data
     */
    private function parseRule($method, $dbField, $field, $data): void
    {
        if (in_array($dbField, $this->tableFields)) {
            switch ($method) {
                case 'year':
                    $this->db->whereYear($dbField, $data[$field]);
                    break;
                case 'month':
                    $this->db->whereMonth($dbField, $data[$field]);
                    break;
                case 'dateBetween':
                    $betweenStart = $data[$field];
                    $field        = str_replace('__start', '__end', $field);
                    $betweenEnd   = $data[$field];
                    $sql = $this->db->whereBetweenTime($dbField, $betweenStart, $betweenEnd);
                    break;
                case 'between':
                    $betweenStart = $data[$field];
                    $field        = str_replace('__between_start', '__between_end', $field);
                    $betweenEnd   = $data[$field];
                    $this->db->whereBetween($dbField, [$betweenStart, $betweenEnd]);
                    break;
                case 'notBetween':
                    $betweenStart = $data[$field];
                    $field        = str_replace('__between_start', '__between_end', $field);
                    $betweenEnd   = $data[$field];
                    $this->db->whereNotBetween($dbField, [$betweenStart, $betweenEnd]);
                    break;
                case 'like':
                    $this->db->whereLike($dbField, "%$data[$field]%");
                    break;
                case 'eq':
                    $this->db->where($dbField, $data[$field]);
                    break;
                case 'cascader':
                    if (is_array($data[$field])) {
                        $this->db->where(function ($q) use ($data, $field) {
                            $q->whereOr($data[$field]);
                        });
                    } else {
                        $this->db->where($dbField, $data[$field]);
                    }
                    break;
                case 'neq':
                    $this->db->where($dbField, '<>', $data[$field]);
                    break;
                case 'egt':
                    $this->db->where($dbField, '>=', $data[$field]);
                    break;
                case 'gt':
                    $this->db->where($dbField, '>', $data[$field]);
                    break;
                case 'elt':
                    $this->db->where($dbField, '<=', $data[$field]);
                    break;
                case 'lt':
                    $this->db->where($dbField, '<', $data[$field]);
                    break;
                case 'findIn':
                    $this->db->whereFindInSet($dbField, $data[$field]);
                    break;
                case 'in':
                    $this->db->whereIn($dbField, $data[$field]);
                    break;
                case 'notIn':
                    $this->db->whereNotIn($dbField, $data[$field]);
                    break;
                case 'json':
                    $this->db->whereRaw("JSON_EXTRACT({$dbField},'$.{$this->jsonNode}') = '{$data[$field]}'");
                    break;
                case 'jsonLike':
                    $this->db->whereRaw("JSON_EXTRACT({$dbField},'$.{$this->jsonNode}') LIKE \"%{$data[$field]}%\"");
                    break;
                case 'jsonArrLike':
                    $this->db->whereRaw("JSON_EXTRACT({$dbField},'$[*].{$this->jsonNode}') LIKE \"%{$data[$field]}%\"");
                    break;
            }
        }
    }

    /**
     * 关联查询
     * @param string $relation_method 关联方法
     * @param mixed $callback
     * @return $this
     * @throws \think\exception\DbException
     */
    protected function relationWhere($relation_method, $callback)
    {
        if (method_exists($this->model, $relation_method)) {
            $relation = $this->model->$relation_method();
            if ($relation instanceof Relation) {
                $relationModel  = get_class($relation->getModel());
                $relation_table = $relation->getTable();
                $foreignKey     = $relation->getForeignKey();
                $pk             = $relation->getLocalKey();
                if ($callback instanceof \Closure) {
                    $this->relationModel = new self(new $relationModel);
                    $this->relationModel->relationLastDb($this->relationLastDb, $this->relationLastMethod);
                    $this->relationModel->setRelationLastDb($relation_method);
                    $this->relationModel->setIfWhere($this->ifWhere);
                    call_user_func($callback, $this->relationModel);
                }
                $tmpDb       = clone $this->relationModel->db();
                $relationSql = $tmpDb->removeWhereField('delete_time')->buildSql();
                $res         = strpos($relationSql, 'WHERE');
                if ($relation instanceof HasMany) {
                    $sql = $this->relationModel->db()->whereRaw("{$relation_table}.{$foreignKey}={$this->db->getTable()}.{$pk}")->buildSql();
                } elseif ($relation instanceof BelongsTo) {
                    $sql = $this->relationModel->db()->whereRaw("{$pk}={$this->db->getTable()}.{$foreignKey}")->buildSql();
                } else if ($relation instanceof HasOne) {
                    $sql = $this->relationModel->db()->whereRaw("{$foreignKey}={$this->db->getTable()}.{$pk}")->buildSql();
                } else if ($relation instanceof MorphOne || $relation instanceof MorphMany) {
                    $reflectionClass = new \ReflectionClass($relation);
                    $propertys       = ['morphKey', 'morphType', 'type'];
                    $propertyValues  = [];
                    foreach ($propertys as $var) {
                        $property = $reflectionClass->getProperty($var);
                        $property->setAccessible(true);
                        $propertyValues[] = $property->getValue($relation);
                    }
                    list($morphKey, $morphType, $typeValue) = $propertyValues;
                    $sql = $this->relationModel->db()->whereRaw("{$morphKey}={$this->db->getTable()}.{$this->db->getPk()}")->where($morphType, $typeValue)->buildSql();
                }
                $this->relationExistSql = $sql;
                if ($res !== false || $this->ifWhere == false) {
                    $this->db->whereExists($sql);
                }
            }
        }
        return $this;
    }

    public function setRelationLastDb($method)
    {
        if ($this->relationLastMethod == $method) {
            $this->db = $this->relationLastDb;
        }
    }

    public function relationLastDb($db, $method)
    {
        $this->relationLastDb     = $db;
        $this->relationLastMethod = $method;
    }

    public function setIfWhere(bool $bool)
    {
        $this->ifWhere = $bool;
    }

    public function getRelationExistSql()
    {
        return $this->relationExistSql;
    }

    /**
     * 返回db对象
     * @return Db
     */
    public function db()
    {
        return $this->db;
    }

    /**
     * @return Form
     */
    public function render()
    {
        if($this->columnNum > 0){
            $formItems = [];
            do{
                $formItem = $this->form->popItem();
                if($formItem){
                    array_unshift($formItems,$formItem);
                }
            }while ($formItem);
            $row = new Row();
            foreach ($formItems as $item){
                $row->column($item,24 / $this->columnNum );
            }
            $this->form->content($row);
        }
        return $this->form;
    }
}
