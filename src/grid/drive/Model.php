<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-01-12
 * Time: 23:43
 */

namespace Eadmin\grid\drive;


use Eadmin\contract\GridInterface;
use Eadmin\grid\Filter;
use think\facade\Db;
use think\facade\Request;
use think\model\relation\BelongsTo;
use think\model\relation\BelongsToMany;
use think\model\relation\HasMany;
use think\model\relation\HasOne;
use think\model\relation\MorphMany;
use think\model\relation\MorphOne;

/**
 * Class Model
 * @package Eadmin\grid\drive
 * @property \think\Model $model
 */
class Model implements GridInterface
{
    //模型
    protected $model;
    //主键字段
    protected $pkField;
    //数据源
    protected $data = [];
    //当前模型的数据库查询对象
    protected $db;
    //关联
    protected $relations = [];
    //表字段
    protected $tableFields = [];
    //软删除字段
    protected $softDeleteField = 'delete_time';

    //是否开启软删除
    protected $isSotfDelete = false;
    //排序字段
    protected $sortField = 'sort';
    //删除前回调
    protected $beforeDel = null;

    public function __construct($model)
    {
        $this->model       = $model;
        $this->db          = $this->model->db();
        $this->tableFields = $this->model->getTableFields();
        $this->pkField     = $this->model->getPk();
        if (in_array($this->softDeleteField, $this->tableFields)) {
            $this->isSotfDelete = true;
            if (request()->has('eadmin_deleted')) {
                $this->db->removeWhereField($this->softDeleteField);
                $this->db->whereNotNull($this->softDeleteField);
            } else {
                $this->db->whereNull($this->softDeleteField);
            }
        }
    }

    /**
     * 是否有回收站
     * @return bool
     */
    public function trashed()
    {
        return $this->isSotfDelete;
    }

    //预关联加载
    protected function withRelations()
    {
        $this->relations = array_unique($this->relations);
        $with            = $this->db->getOptions('with');
        if (is_null($with)) {
            $with = [];
        }
        $with = array_merge($with, $this->relations);
        if (count($with) > 0) {
            $this->db->with($with);
        }
    }

    /**
     * 解析关联方法设置
     * @param string $relation 关联方法
     */
    public function realiton($relation)
    {
        $fields = explode('.', $relation);
        if (count($fields) > 1) {
            array_pop($fields);
            $relation = implode('.', $fields);
            foreach ($fields as $field) {
                $this->setRelation($field, $relation);
            }
        } else {
            $this->setRelation($relation, $relation);
        }
    }

    public function getData(bool $hidePage, int $page, int $size)
    {
        $this->withRelations();
        if ($hidePage) {
            return $this->db->select();;
        } else {
            return $this->db->page($page, $size)->select();
        }
    }

    public function getPk()
    {
        return $this->pkField;
    }

    //获取数据总条数
    public function getTotal(): int
    {
        if ($this->db) {
            $sql   = $this->db->buildSql();
            $sql   = "SELECT COUNT(*) FROM {$sql} userCount";
            $res   = \think\facade\Db::query($sql);
            $count = $res[0]['COUNT(*)'];
        } else {
            $count = count($this->data);
        }
        return $count;
    }

    /**
     * 设置预加载关联
     * @param string $ifRelation 关联方法名称
     * @param mixed $relation 关联方法
     */
    private function setRelation($ifRelation, $relation)
    {
        if (method_exists($this->model, $ifRelation) &&
            ($this->model->$ifRelation() instanceof BelongsTo ||
                $this->model->$ifRelation() instanceof HasOne ||
                $this->model->$ifRelation() instanceof HasMany ||
                $this->model->$ifRelation() instanceof MorphOne ||
                $this->model->$ifRelation() instanceof MorphMany)) {
            $this->relations[] = $relation;
        }
    }

    //快捷搜索
    public function quickFilter($keyword, $columns)
    {
        if ($keyword) {
            $whereFields         = [];
            $whereOr             = [];
            $relationWhereFields = [];
            $relationWhereOr     = [];
            foreach ($columns as $column) {
                $field  = $column->getField();
                $fields = explode('.', $field);
                $field  = end($fields);
                $usings = $column->getUsing();
                if (count($fields) > 1) {
                    array_pop($fields);
                    $relation = array_pop($fields);;
                    if (empty($usings)) {
                        $relationWhereFields[$relation][] = $field;
                    } else {
                        foreach ($usings as $key => $value) {
                            if (strpos($value, $keyword) !== false) {
                                $relationWhereOr[$relation][$field] = $key;
                            }
                        }
                    }
                } else {
                    if (in_array($column->getField(), $this->tableFields)) {
                        if (empty($usings)) {
                            $whereFields[] = $field;
                        } else {
                            foreach ($usings as $key => $value) {
                                if (stripos($value, $keyword) !== false) {
                                    $whereOr[$field] = $key;
                                }
                            }
                        }
                    }
                }
            }
            //快捷搜索
            $relationWhereSqls = [];
            foreach ($this->relations as $relationName) {
                $relations      = explode('.', $relationName);
                $Tmprelations   = $relations;
                $relation       = array_pop($Tmprelations);
                $relationFilter = implode('.', $relations);
                $model          = get_class($this->model);
                $filter         = new Filter(new $model);
                $filter->setIfWhere(false);
                $db = $this->model;
                foreach ($relations as $relation) {
                    $db = $db->getModel()->$relation();
                }
                $filter->relationLastDb($db, $relation);
                $relationName        = $relation;
                $relationTableFields = $db->getTableFields();
                if (isset($relationWhereFields[$relationName])) {
                    $relationWhereFields[$relationName] = array_intersect($relationWhereFields[$relationName], $relationTableFields);
                    $fields                             = implode('|', $relationWhereFields[$relationName]);
                    $relationWhereCondtion              = $relationWhereOr[$relationName] ?? [];
                    $db->where(function ($q) use ($fields, $keyword, $relationWhereCondtion) {
                        foreach ($relationWhereCondtion as $field => $value) {
                            $q->whereOr($field, $value);
                        }
                        $q->whereLike($fields, "%{$keyword}%", 'OR');
                    });
                    $filter->parseFilter(null, $relationFilter . '.');
                    $wheres = $filter->db()->getOptions('where');
                    foreach ($wheres['AND'] as $where) {
                        if ($where[1] == 'EXISTS' && !empty($fields)) {
                            $relationWhereSqls[] = $where[2];
                            break;
                        }
                    }
                }
            }
            $fields = implode('|', $whereFields);
            $this->db->where(function ($q) use ($relationWhereSqls, $fields, $keyword, $whereOr) {
                $q->whereLike($fields, "%{$keyword}%", 'OR');
                foreach ($whereOr as $field => $value) {
                    $q->whereOr($field, $value);
                }
                foreach ($relationWhereSqls as $sql) {
                    $q->whereExists($sql, 'OR');
                }
            });
        }
    }

    /**
     * 更新数据
     * @param array $ids 更新条件id
     * @param array $data 更新数据
     * @return Model
     */
    public function update(array $ids, array $data)
    {
        $action = isset($data['action']) ? $data['action'] : '';
        $pk = $this->model->getPk();
        if ($action == 'eadmin_sort') {
            $field   = "{$pk},(@rownum := @rownum+1),case when @rownum = {$data['sort']} then @rownum := @rownum+1 else @rownum := @rownum end AS rownum";
            $sortSql = $this->db->table("(SELECT @rownum := -1) r," . $this->model->getTable())
                ->fieldRaw($field)
                ->removeOption('order')
                ->order($this->sortField)
                ->where($pk, '<>', $data['id'])
                ->buildSql();
            $this->model->where($pk, $data['id'])->update([$this->sortField => $data['sort']]);
            $res = Db::execute("update {$this->model->getTable()} inner join {$sortSql} a on a.{$pk}={$this->model->getTable()}.{$pk} set {$this->sortField}=a.rownum");
            admin_success('操作完成', '排序成功');
        } else {
            $res = $this->model->removeWhereField($this->softDeleteField)->strict(false)->whereIn($pk, $ids)->update($data);
            if ($res !== false) {
                admin_success('操作完成', '数据保存成功')->redirect($url);
            } else {
                admin_error_message('数据保存失败');
            }
        }
    }

    /**
     * @param string $sortField
     */
    public function sortField(string $sortField): void
    {
        $this->sortField = $sortField;
    }

    /**
     * 删除数据
     * @param bool|array $ids 要删除的id true数据清空
     */
    public function destroy($ids)
    {
        $trueDelete = Request::delete('trueDelete');
        $res        = true;
        Db::startTrans();
        try {
            $this->db->removeWhereField($this->softDeleteField);
            if ($ids === true) {
                if ($this->isSotfDelete && !$trueDelete) {
                    $res = $this->db->where('1=1')->update([$this->softDeleteField => date('Y-m-d H:i:s')]);
                } else {
                    if (in_array($this->softDeleteField, $this->tableFields)) {
                        $deleteData = $this->db->field($this->model()->getPk())->whereNotNull($this->softDeleteField)->select();
                    } else {
                        $deleteData = $this->db->field($this->model()->getPk())->select();
                    }
                    $this->deleteRelationData($deleteData);
                }
            } else {
                if ($this->isSotfDelete && !$trueDelete) {
                    $res = Db::name($this->model->getTable())->whereIn($this->model->getPk(), $ids)->update([$this->softDeleteField => date('Y-m-d H:i:s')]);
                } else {
                    $deleteData = $this->model->field($this->model()->getPk())->removeOption('where')->whereIn($this->model->getPk(), $ids)->select();
                    $this->deleteRelationData($deleteData);
                }
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $res = false;
        }
        return $res;
    }

    /**
     * 删除关联数据
     * @param mixed $deleteData
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function deleteRelationData($deleteData)
    {

        $reflection    = new \ReflectionClass($this->model);
        $methods       = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        $className     = $reflection->getName();
        $relationMethod = [];
        foreach ($methods as $method) {
            if ($method->class == $className) {
                $relation = $method->name;
                $p        = new \ReflectionMethod($method->class, $relation);
                if ($p->getNumberOfParameters() == 0 && !$p->isStatic()) {
                    if ($this->model->$relation() instanceof BelongsToMany) {
                        foreach ($deleteData as $data) {
                            $data->$relation()->detach();
                        }
                    } elseif ($this->model->$relation() instanceof HasOne) {
                        foreach ($deleteData as $data) {
                            if (!is_null($data->$relation)) {
                                $data->$relation->delete();
                            }
                        }
                    }
                }
            }
        }
        //模型全局查询影响，这里用db删除
        $deleteIds = $deleteData->column($this->model->getPk());
        Db::name($this->model->getTable())->delete($deleteIds);
    }

    /**
     * 获取当前模型的数据库查询对象
     * @return Model
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * 获取当前模型的数据库查询对象
     * @return \think\Db
     */
    public function db()
    {
        return $this->db;
    }

}
