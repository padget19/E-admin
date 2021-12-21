<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-01-01
 * Time: 09:22
 */

namespace Eadmin\form;


use Eadmin\Admin;
use Eadmin\component\basic\Button;
use Eadmin\component\basic\Card;
use Eadmin\component\basic\Html;
use Eadmin\component\basic\Step;
use Eadmin\component\basic\Steps;
use Eadmin\component\basic\TabPane;
use Eadmin\component\basic\Tabs;
use Eadmin\component\Component;
use Eadmin\component\form\Field;
use Eadmin\component\form\field\Cascader;
use Eadmin\component\form\field\CheckboxGroup;
use Eadmin\component\form\field\DatePicker;
use Eadmin\component\form\field\Display;
use Eadmin\component\form\field\Input;
use Eadmin\component\form\field\Map;
use Eadmin\component\form\field\Select;
use Eadmin\component\form\field\TimePicker;
use Eadmin\component\form\FormAction;
use Eadmin\component\form\FormItem;
use Eadmin\component\form\FormMany;
use Eadmin\component\form\step\FormSteps;
use Eadmin\component\form\step\Result;
use Eadmin\component\layout\Row;
use Eadmin\contract\FormInterface;
use Eadmin\form\traits\ComponentForm;
use Eadmin\form\traits\WatchForm;
use Eadmin\traits\CallProvide;
use Eadmin\traits\Exec;
use Eadmin\traits\FormModel;
use think\facade\Request;
use think\helper\Str;
use think\Model;

/**
 * 表单
 * Class Form
 * @link https://element-plus.gitee.io/#/zh-CN/component/form
 * @package Eadmin\component\form\field
 * @method \Eadmin\component\form\field\Input text($field, $label = '') 文本输入框
 * @method \Eadmin\component\form\field\Input hidden($field) 隐藏输入框
 * @method \Eadmin\component\form\field\Input textarea($field, $label = '') 多行文本输入框
 * @method \Eadmin\component\form\field\Input password($field, $label = '') 密码输入框
 * @method \Eadmin\component\form\field\Mobile mobile($field, $label = '') 手机号输入框
 * @method \Eadmin\component\form\field\Email email($field, $label = '') 邮箱输入框
 * @method \Eadmin\component\form\field\Number number($field, $label = '') 数字输入框
 * @method \Eadmin\component\form\field\Money money($field, $label = '') 金额输入框
 * @method \Eadmin\component\form\field\Select select($field, $label = '') 下拉选择器
 * @method \Eadmin\component\form\field\RadioGroup radio($field, $label = '') 单选框
 * @method \Eadmin\component\form\field\CheckboxGroup checkbox($field, $label = '') 多选框
 * @method \Eadmin\component\form\field\Switchs switch ($field, $label = '') switch开关
 * @method \Eadmin\component\form\field\DatePicker datetime($field, $label = '') 日期时间
 * @method \Eadmin\component\form\field\DatePicker datetimeRange($startFiled, $endField, $label = '') 日期时间范围时间
 * @method \Eadmin\component\form\field\DatePicker dateRange($startFiled, $endField, $label = '') 日期范围时间
 * @method \Eadmin\component\form\field\DatePicker date($field, $label = '') 日期
 * @method \Eadmin\component\form\field\DatePicker dates($field, $label = '') 多选日期
 * @method \Eadmin\component\form\field\DatePicker year($field, $label = '') 年
 * @method \Eadmin\component\form\field\DatePicker month($field, $label = '') 月
 * @method \Eadmin\component\form\field\TimePicker timeRange($startFiled, $endField, $label = '') 时间范围时间
 * @method \Eadmin\component\form\field\TimePicker time($field, $label = '') 时间
 * @method \Eadmin\component\form\field\Slider slider($field, $label = '') 滑块
 * @method \Eadmin\component\form\field\Color color($field, $label = '') 颜色选择器
 * @method \Eadmin\component\form\field\Rate rate($field, $label = '') 评分组件
 * @method \Eadmin\component\form\field\Upload file($field, $label = '') 文件上传
 * @method \Eadmin\component\form\field\Upload image($field, $label = '') 图片上传
 * @method \Eadmin\component\form\field\Editor editor($field, $label = '') 富文本编辑器
 * @method \Eadmin\component\form\field\Tree tree($field, $label = '') 树形
 * @method \Eadmin\component\form\field\Cascader cascader(...$field, $label = '') 级联选择器
 * @method \Eadmin\component\form\field\Transfer transfer($field, $label = '') 穿梭框
 * @method \Eadmin\component\form\field\Icon icon($field, $label = '') 图标选择器
 * @method \Eadmin\component\form\field\SelectTable selectTable($field, $label = '') 表格选择器  TODO
 * @method \Eadmin\component\form\field\Map maps($lng, $lat, $address, $label = '') 高德地图  TODO
 * @method \Eadmin\component\form\field\DynamicTag tag($field, $label = '') 动态标签
 * @method \Eadmin\component\form\field\Checktag checkTag($field, $label = '') check标签
 * @method \Eadmin\component\form\field\Spec spec($field, $label = '') 规格
 * @method \Eadmin\component\form\field\Display display($field, $label = '') 显示
*/
class Form extends Component
{
    use CallProvide, ComponentForm, WatchForm;

    protected $name = 'EadminForm';
    protected $actions;
    public $tab;
    protected $steps;


    protected $manyRelation = '';
    protected $itemComponent = [];
    protected $formItem = [];

    //是否编辑表单
    protected $isEdit = false;

    protected $drive;

    protected $validator;

    protected $data = [];
    //保存前回调
    protected $beforeSave = null;
    //保存后回调
    protected $afterSave = null;
    //保存修改成功后跳转的url
    protected $redirectUrl = '';

    protected $batch = false;
    //排除字段
    protected $exceptField = [];
    //初始化
    protected static $init = null;

    public function __construct($data)
    {
        if ($data instanceof Model) {
            $this->drive = new \Eadmin\form\drive\Model($data);
        } elseif ($data instanceof FormInterface) {
            $this->drive = $data;
        } else {
            $this->drive = new \Eadmin\form\drive\Arrays($data);
        }
        $field = Str::random(15, 3);
        $this->attr('exceptField', $this->exceptField);
        $this->bindAttr('model', $field);
        $this->attr('formField',$field);
        $this->bindAttValue('submit', false, true);
        $this->bindAttValue('reset', false, true);
        $this->bindAttValue('validate', false, true);
        $this->actions = new FormAction($this);
        $this->labelWidth('100px');
        $this->parseCallMethod();
        $this->setAction('/eadmin.rest');
        $this->event('gridRefresh', []);
        $this->validator = new ValidatorForm();
        $this->validatorBind();
        $this->description(Request::param('eadmin_description'));
        if (!is_null(self::$init)) {
            call_user_func(self::$init, $this);
        }
    }

    public static function create($data, \Closure $closure)
    {
        $self = new self($data);
        $self->parseCallMethod(true, 2);
        $self->setExec($closure);
        return $self;
    }

    /**
     * 初始化
     * @param \Closure $closure
     */
    public static function init(\Closure $closure)
    {
        self::$init = $closure;
    }

    /**
     * 设置标题
     * @param string $title
     * @return string
     */
    public function title(string $title)
    {
        return $this->bind('eadmin_title', $title);
    }

    /**
     * 居中对齐
     * @param int $width 宽度
     */
    public function alignCenter(int $width = 1000)
    {
        if(empty($width)){
            $this->removeAttr('style');
        }else{
            $this->attr('style', ['width' => $width .'px', 'margin' => '0 auto']);
        }
    }

    /**
     * @return ValidatorForm
     */
    public function validator()
    {
        return $this->validator;
    }

    /**
     * 是否在输入框中显示校验结果反馈图标
     * @param bool $value
     * @return $this
     */
    public function statusIcon(bool $value = true)
    {
        $this->attr(__FUNCTION__, $value);
        return $this;
    }

    /**
     * 是否显示必填字段的标签旁边的红色星号
     * @param bool $value
     * @return $this
     */
    public function hideRequiredAsterisk(bool $value = true)
    {
        $this->attr(__FUNCTION__, $value);
        return $this;
    }

    /**
     * 设置保存修改成功后跳转的url
     * @param string $url
     */
    public function redirectUrl($url = '')
    {
        if ($url) {
            $this->redirectUrl = $url;
        }
        return $this->redirectUrl;
    }

    /**
     * 修改成功后后退
     * @return string
     */
    public function redirectBack()
    {
        $this->redirectUrl = 'back';
    }

    /**
     * 行内表单模式
     * @param bool $bool
     * @return $this
     */
    public function inline(bool $bool = true)
    {
        $this->attr(__FUNCTION__, $bool);
        return $this;
    }

    /**
     * 表单域标签的后缀
     * @param string $value
     * @return $this
     */
    public function labelSuffix(string $value)
    {
        $this->attr(__FUNCTION__, $value);
        return $this;
    }

    /**
     * 表单域标签的宽度
     * @param string $value 例如 '50px'
     * @return $this
     */
    public function labelWidth(string $value)
    {
        $this->attr(__FUNCTION__, $value);
        return $this;
    }

    /**
     * 表单域标签的位置
     * @param string $value right/left/top
     * @return $this
     */
    public function labelPosition(string $value)
    {
        $this->attr(__FUNCTION__, $value);
        return $this;
    }

    /**
     * 尺寸
     * @param string $value medium / small / mini
     * @return $this
     */
    public function size(string $value)
    {
        $this->attr(__FUNCTION__, $value);
        return $this;
    }

    /**
     * 底部元素
     * @param $content
     * @return $this
     */
    public function footer($content)
    {
        $html = Html::create($content)->tag('div');

        //按步骤表单显示
        $backtrace          = debug_backtrace(1, 5);
        if($this->steps && $backtrace[4]['class'] == FormSteps::class  && $backtrace[4]['function'] == 'add'){
            $active = $this->steps->bindAttr('current');
            $count = $this->steps->count();
            $html->where($active, $count);
        }

        $this->content($html, 'footer');
        return $this;
    }

    /**
     * 设置提交url
     * @param string $sumbitUrl 提交url
     * @param string $method 提交method
     * @return $this
     */
    public function setAction(string $sumbitUrl, string $method = 'POST')
    {
        $this->attr(__FUNCTION__, $sumbitUrl);
        $this->attr('setActionMethod', $method);
        return $this;
    }

    /**
     * 添加内容
     * @param mixed $content
     * @param string $name 插槽名称默认即可default
     * @return static
     */
    public function content($content, $name = 'default')
    {
        if ($content instanceof Field && $content->bindAttr('modelValue')) {
            $this->valueModel($content);
        }
        return parent::content($content, $name);
    }

    /**
     * 绑定值到form
     * @param mixed $component 组件
     * @param null $data
     */
    private function valueModel($component, $data = null)
    {
        foreach ($component->bindAttribute as $attr => $field) {
            $value = $this->drive->getData($field, $data);

            if (!empty($value)) {
                $component->bind($field, $value);
            }
        }

        foreach ($component->bindAttribute as $attr => $field) {
            $value = $this->drive->getData($field, $data);
            $defaultValue = $component->getDefault();
            $componentValue = $component->getValue();
            //设置default缺省值
            if (empty($value) && $value !== '0' && $value !== 0 && !is_null($defaultValue)) {
                $value = $this->getPickerValue($component, $field, $defaultValue);
            }

            //value固定值
            if (!is_null($componentValue)) {
                $value = $this->getPickerValue($component, $field, $componentValue);
            }
            if ($attr != 'modelValue' && $component->bind($field)) {
                $value = $component->bind($field);
            }
            //级联relation解析关联数据bind回显
            if ($component instanceof Cascader && $attr == 'relation') {
                $cascaderField = explode('.', $component->bindAttr('modelValue'));
                $cascaderField = array_pop($cascaderField);
                $this->setData($cascaderField, $component->parseRelationData($value));
            }
            //display组件定义显示
            if ($component instanceof Display && $component->getClosure() instanceof \Closure) {
                $value = call_user_func($component->getClosure(), $value, $data);
            }
            if ($component instanceof DatePicker || $component instanceof TimePicker) {
                $value = empty($value) ? null : $value;
                $this->setData($field, $value);
            }elseif (($component instanceof Cascader || $component instanceof Map) && $attr != 'modelValue'){
                if(is_array($value)) {
                    $val = array_shift($value);
                    $this->setData($field, $val);
                    $component->default($value);
                    $component->value($value);
                }
            }elseif ($component instanceof Map && $attr == 'modelValue' && is_array($value)){
                $this->setData($field, end($value));
            } else {
                $this->setData($field, $value ?? '');
            }
            if (is_null($data)) {
                $component->bindAttr($attr, $this->bindAttr('model') . '.' . $field, true);
            }
            $component->removeBind($field);
        }
    }

    /**
     * 获取时间value特殊处理
     * @param mixed $component 组件
     * @param string $field 字段
     * @param mixed $componentValue 值
     * @return mixed
     */
    private function getPickerValue($component, $field, $componentValue)
    {
        $value = $componentValue;
        if ($component instanceof DatePicker || $component instanceof TimePicker) {
            $startField = $component->bindAttr('startField');
            $endField = $component->bindAttr('endField');
            if ($field == $startField && isset($componentValue[0])) {
                $value = $componentValue[0];
            }
            if ($field == $endField && isset($componentValue[1])) {
                $value = $componentValue[1];
            }
        }
        return $value;
    }

    /**
     * 表单步骤条
     * @param int $current 初始步数
     * @return FormSteps
     */
    public function steps($current = 0)
    {
        $this->steps = new FormSteps($this);
        $this->steps->bindAttValue('current', $current);
        $prop = $this->steps->bindAttr('current');
        $this->except([$prop]);
        $this->push($this->steps);
        $this->bindAttr('step', $this->steps->bindAttr('current'), true);
        return $this->steps;
    }

    public function parseSteps()
    {

        if ($this->steps && isset($this->steps->content['default'])) {
            $validateField = $this->bindAttr('validate');
            $active = $this->steps->bindAttr('current');
            $count = count($this->steps->content['default']);
            for ($i = 0; $i <= $count; $i++) {
                if ($i < ($count - 1) && $i > 0) {
                    $back = $i - 1;
                    $this->actions->addLeftAction(
                        Button::create('上一步')
                            ->sizeMedium()
                            ->where($active, $i)
                            ->event('click', [$active => $back])
                    );
                }
                if ($count - 2 > $i) {
                    $next = $i + 1;
                    $this->actions->addLeftAction(
                        Button::create('下一步')
                            ->sizeMedium()
                            ->where($active, $i)
                            ->event('click', [$validateField => true])
                    );
                }
            }
            $this->actions->style(['margin:0 80px']);
            $this->actions->submitButton()->where($active, $count - 2);
            $this->actions->hideResetButton();
        }
    }

    /**
     * 选项卡布局
     * @param string $title 标题
     * @param \Closure $closure
     * @param int $index 默认选项卡
     * @return $this
     */
    public function tab($title, \Closure $closure,$index=1)
    {
        if (!$this->tab) {
            $this->tab = Tabs::create()->default($index);
            $prop = $this->tab->bindAttr('modelValue');
            $this->attr('tabField',$prop);
            $this->except([$prop]);
            $this->push($this->tab);
        }
        $prop = $this->tab->bindAttr('modelValue');
        $formItems = $this->collectFields($closure);
        $tabPane = new TabPane();
        $name = $this->tab->getContentCount();
        $tabPane->name($name)->label($title);
        $tabPane->content($formItems);
        $this->tab->content($tabPane);
        return $this;
    }

    public function collectFields(\Closure $closure)
    {
        $offset = count($this->formItem);
        call_user_func($closure, $this);
        $formItems = array_slice($this->formItem, $offset);
        $this->formItem = array_slice($this->formItem, 0, $offset);
        return $formItems;
    }

    public function push($item)
    {
        $this->formItem[] = $item;
    }

    /**
     * 添加item
     * @param string $prop 字段
     * @param string $label 标签
     * @return FormItem
     */
    public function item($prop = '', $label = '')
    {
        $item = FormItem::create($prop, $label, $this);
        if (empty($this->manyRelation)) {
            $ifField = str_replace('.', '_', $prop);
            $ifField = $this->bindAttr('model') . $ifField . 'Show';
            $this->bind($ifField, 1);
            $item->where($ifField, 1);
        }
        $this->push($item);
        return $item;
    }

    /**
     * 添加一行布局
     * @param string $title
     * @param \Closure $closure
     * @param int $gutter
     * @return $this
     */
    public function row(\Closure $closure, string $title = '', $gutter = 0)
    {

        $row = new Row();
        if ($gutter > 0) {
            $row->gutter($gutter);
        }
        $formItems = $this->collectFields($closure);
        if (!empty($title)) {
            $this->push("<h4 style='font-size:16px;'>{$title}</h4>");
        }
        foreach ($formItems as $item) {
            $column = $row->column($item, $item->md);
            $column->setWhere($item->getWhere());
        }
        $this->push($row);
        return $row;
    }

    /**
     * 是否编辑
     * @return bool
     */
    public function isEdit()
    {
        $pkValue = Request::param($this->drive->getPk());
        if ($pkValue && !$this->isEdit) {
            $this->edit($pkValue);
        }
        return $this->isEdit;
    }

    /**
     * 编辑
     * @param string|int $id 主键id数据
     * @return $this
     */
    public function edit($id)
    {
        $this->drive->edit($id);
        $this->isEdit = true;
        $this->attr('editId', $id);
        $this->setAction('/eadmin/' . $id . '.rest', 'PUT');
        return $this;
    }

    public function manyRelation($relation = null)
    {
        if (is_null($relation)) {
            return $this->manyRelation;
        }
        $this->manyRelation = $relation;
    }

    public function batch(\Closure $closure, $data = [])
    {
        $this->batch = true;
        $manyItem = $this->hasMany('eadmin_batch', '', $closure);
        if (count($data) == 0) {
            $data[] = $manyItem->attr('manyData');
        } else {
            foreach ($data as &$row) {
                $row = array_merge($manyItem->attr('manyData'), $row);
            }
        }
        $manyItem->value($data);
        return $manyItem;
    }

    /**
     * 一对多添加
     * @param mixed $relation 关联方法|字段
     * @param string $title 标题
     * @param \Closure $closure
     */
    public function hasMany($relation, $title, \Closure $closure)
    {
        $this->validatorBind($relation);

        $manyItem = FormMany::create($relation, []);
        $validatorField = $this->bindAttr('model') . 'Error';
        $manyItem->attr('validator', $validatorField);
        $originItemComponent = $this->itemComponent;
        $this->itemComponent = [];
        $this->manyRelation = $relation;
        $formItems = $this->collectFields($closure);
        $this->manyRelation = '';
        $itemComponent = $this->itemComponent;
        $datas = $this->getData($relation) ?: [];
        $manyData = [];

        foreach ($itemComponent as $component) {
            $componentClone = clone $component;
            $this->valueModel($componentClone, []);
        }

        $manyItem->attr('manyData', $this->data);
        if (!$this->isEdit && empty($datas)) {
            //添加模式默认添加一条
            $datas[] = $this->data;
        }
        $this->data = [];
        $manyItem->attr('field', $relation);
        $manyItem->attr('title', $title);
        foreach ($datas as $key => $data) {
            foreach ($itemComponent as $component) {
                if (count($datas) - 1 == $key) {
                    $componentClone = $component;
                } else {
                    $componentClone = clone $component;
                }
                $this->valueModel($componentClone, $data);
            }
            if ($data instanceof Model) {
                $pk = $data->getPk();
                $this->data[$pk] = $data[$pk];
            }
            $manyData[] = $this->data;
            $this->data = [];
        }

        $manyItem->value($manyData);
        $this->itemComponent = $originItemComponent;
        foreach ($formItems as $item) {
            $formItem = clone $item;
            $columns[] = [
                'title'=>$formItem->attr('label'),
                'dataIndex'=>$formItem->attr('prop'),
                'prop'=>$formItem->attr('prop'),
                'component'=>$formItem
            ];
            $formItem->removeAttr('label');
            $manyItem->content($item);
        }
        $manyItem->attr('columns',$columns);
        $this->push($manyItem);
        return $manyItem;
    }

    protected function formItem($name, $arguments)
    {
        $field = $arguments[0];
        if (count($arguments) > 1) {
            $label = array_pop($arguments);
        }
        $label = $label ?? '';

        $inputs = [
            'text',
            'textarea',
            'password',
            'hidden',
        ];
        $dates = [
            'date',
            'dates',
            'year',
            'month',
            'datetime',
            'datetimeRange',
            'dateRange',
        ];
        $times = [
            'time',
            'timeRange',
        ];
        $class = self::$component[$name];
        $prop = $field;
        $component = $class::create($field);
        $componentArr = array_merge($inputs, $dates, $times);
        if ($name == 'image') {
            //图片组件
            $component->displayType('image')->accept('image/*')->size(120, 120)->isUniqidmd5();
        }
        if (in_array($name, $componentArr)) {
            if ($component instanceof TimePicker || $component instanceof DatePicker) {
                //由于element时间范围字段返回是一个数组,这里特殊绑定处理成2个字段
                if ($name == 'dateRange' || $name == 'datetimeRange' || $name == 'timeRange') {
                    $component = $class::create();
                    $component->rangeField($field, $arguments[1]);
                    $component->startPlaceholder('请选择开始时间');
                    $component->endPlaceholder('请选择结束时间');
                    $this->except([$component->bindAttr('timeValue')]);
                }
                $prop = $component->bindAttr('modelValue');
                $this->except([$prop]);
            }
            $component->type($name);
            if ($name === 'password') {
                $component->showPassword();
            }
        }
        if ($name == 'cascader') {
            $component = $class::create();
            $component->attr('bindFields', $arguments);
            $component->bindFields($arguments);
            $prop = $component->bindAttr('modelValue');
            $this->except([$prop]);
        } elseif ($name == 'maps') {
            $field = array_pop($arguments);
            $component = $class::create($field);
            $component->bindFields($arguments);
            $prop = $component->bindAttr('modelValue');
        }
        if ($component instanceof Input) {
            $component->placeholder('请输入' . $label);
        } elseif ($component instanceof Select || $component instanceof Cascader) {
            $component->placeholder('请选择' . $label);
        }
        $item = $this->item($prop, $label);
        $item->attr('validateField', $field);
        $item->content($component);
        $component->setFormItem($item);
        if ($name == 'hidden') {
            //隐藏域
            $item->attr('style', ['display' => 'none']);
        }
        return $component;
    }

    /**
     * 保存后回调
     * @param \Closure $closure
     */
    public function saved(\Closure $closure)
    {
        $this->afterSave = $closure;
    }

    /**
     * 保存前回调
     * @param \Closure $closure
     */
    public function saving(\Closure $closure)
    {
        $this->beforeSave = $closure;
    }

    /**
     * 设置字段值
     * @param string $field 字段
     * @param mixed $value 值
     */
    public function setData(string $field, $value)
    {

        //数字类型转换处理
        if (is_array($value) && count($value) == count($value, 1)) {
            foreach ($value as &$v) {
                if (!is_array($v) && preg_match('/^\d{1,11}$/', $v)) {
                    $v = intval($v);
                } elseif (is_numeric($v) && strpos($v, '.') !== false) {
                    $v = floatval($v);
                }
            }
        } elseif (!is_array($value) && preg_match('/^\d{1,11}$/', $value)) {
            $value = intval($value);
        } elseif (is_numeric($value) && strpos($value, '.') !== false) {
            $value = floatval($value);
        }

        if (strpos($field, '.')) {
            [$relation, $field] = explode('.', $field);
            $this->data[$relation][$field] = $value;
        } else {
            $this->data[$field] = $value;
        }
    }

    public function getData($field = null)
    {
        $this->isEdit();
        return $this->drive->getData($field);
    }

    public function getDrive()
    {
        return $this->drive;
    }

    /**
     * 表单操作定义
     * @param \Closure $closure
     * @return FormAction
     */
    public function actions(\Closure $closure = null)
    {
        if ($closure) {
            call_user_func_array($closure, [$this->actions]);
        }
        return $this->actions;
    }

    public function itemComponent()
    {
        return $this->itemComponent;
    }

    public function setItemComponent($component)
    {
        if (is_array($component)) {
            $this->itemComponent = $component;
        } else {
            $this->itemComponent[] = $component;
        }

    }

    /**
     * 保存数据
     * @param array $data 数据
     * @return bool
     * @throws \Exception
     */
    public function save(array $data)
    {
        $this->exec();
        //监听watch
        $this->watchCall($data);
        //验证数据
        $validatorMode = $this->isEdit() ? 2 : 1;
        $this->validator->check($data, $validatorMode);
        //保存前回调
        if (!is_null($this->beforeSave)) {
            $beforeData = call_user_func($this->beforeSave, $data);
            if (is_array($beforeData)) {
                $data = array_merge($data, $beforeData);
            }
        }
        if ($this->batch) {
            $result = $this->drive->saveAll($data['eadmin_batch']);
        } else {
            $result = $this->drive->save($data);
        }

        //保存回后调
        if (!is_null($this->afterSave)) {
            call_user_func_array($this->afterSave, [$data, $this->drive->model()]);
        }
        //步骤表单
        if (isset($data['eadmin_step'])) {
            $id = $this->drive->model()[$this->drive->getPk()];
            call_user_func_array($this->steps->getClosure(), [new Result($data, $result, $id)]);
        }
        return $result;
    }

    /**
     * 提交成功事件
     * @param array $value
     * @return $this
     */
    public function eventSuccess(array $value)
    {
        $this->event('success', $value);
        return $this;
    }

    public function __get($name)
    {
        return $this->getData($name);
    }

    public function __call($name, $arguments)
    {
        return $this->formItem($name, $arguments);
    }

    /**
     * @return FormItem
     */
    public function getLastItem()
    {
        $item = end($this->formItem);
        return $item;
    }
    public function popItem()
    {
        $item = array_pop($this->formItem);
        return $item;
    }

    /**
     * 解析组件
     */
    protected function parseComponent()
    {
        foreach ($this->formItem as $item) {
            $this->content($item);
            if ($item instanceof Tabs) {
                foreach ($item->content['default'] as $pane) {
                    foreach ($pane->content['default'] as $content) {
                        if ($content instanceof FormMany) {
                            $this->valueModel($content);
                        }
                    }
                }
            }
        }
        foreach ($this->itemComponent as $component) {
            //各个组件绑定值赋值
            $this->valueModel($component);
        }
        $field = $this->bindAttr('model');
        $this->data = array_merge($this->callParams, $this->callMethod, $this->data);
        //主键值
        if ($this->isEdit) {
            $pk = $this->drive->getPk();
            $this->data[$pk] = $this->drive->getData($pk);
        }
        //步骤表单
        if ($this->steps) {
            $this->setData('eadmin_step', $this->steps->bindAttr('current'));
            $this->setData('eadmin_step_reset', $this->bindAttr('reset'));
        }
        //将值绑定到form
        $this->bind($field, $this->data);
        //tab验证字段
      
        $this->attr('tabValidateField',$this->validator->getTabField());
    }

    /**
     * 验证错误字段初始化绑定
     * @param null $field
     */
    public function validatorBind($field = null)
    {
        //验证器属性绑定
        $validatorField = $this->bindAttr('model') . 'Error';
        $this->attr('validator', $validatorField);
        if (is_null($field)) {
            $data = [];
        } else {
            $data = $this->bind($validatorField);
            $data[$field] = [];
        }
        $this->bind($validatorField, $data);
    }

    /**
     * 排除字段数据
     * @param array $fields
     */
    public function except(array $fields)
    {
        $this->exceptField = array_merge($this->exceptField, $fields);
        //排除字段
        $this->attr('exceptField', $this->exceptField);
    }

    public static function extend($name, $component)
    {
        self::$component[$name] = $component;
    }

    public function jsonSerialize()
    {

        $this->exec();
        $this->parseComponent();
        $this->parseSteps();
        $this->actions->render();
        return parent::jsonSerialize(); // TODO: Change the autogenerated stub
    }
}
