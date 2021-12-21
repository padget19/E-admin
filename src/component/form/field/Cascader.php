<?php


namespace Eadmin\component\form\field;


use Eadmin\Admin;
use Eadmin\component\form\Field;

/**
 * 级联选择器
 * Class Cascader
 * @link https://element-plus.gitee.io/#/zh-CN/component/cascader
 * @method $this size(string $size) 尺寸 medium / small / mini
 * @method $this placeholder(string $text) 输入框占位文本
 * @method $this clearable(bool $value = true) 是否支持清空选项
 * @method $this showAllLevels(bool $value = true) 输入框中是否显示选中值的完整路径
 * @method $this collapseTags(bool $value = true) 多选模式下是否折叠Tag
 * @method $this separator(string $symbol) 选项分隔符
 * @method $this filterable(bool $value = true) 是否可搜索选项
 * @method $this debounce(int $num) 搜索关键词输入的去抖延迟，毫秒
 * @method $this popperClass(string $class) 自定义浮层类名
 * @package Eadmin\component\form\field
 */
class Cascader extends Field
{
    protected $name = 'ElCascader';
    protected $props = [];
    protected $fields = [];
    protected $initDefault = true;

    public function __construct($field = null, string $value = '')
    {
        parent::__construct($field, $value);
        $this->default = [];
        $this->clearable();
        $this->filterable();
        $this->props('value', 'id');
        $this->props('expandTrigger', 'hover');
    }

    function default($value)
    {
        $this->initDefault = false;
        return parent::default($value); // TODO: Change the autogenerated stub
    }

    /**
     * 面板样式
     * @return $this
     */
    public function panel()
    {
        $this->name = 'ElCascaderPanel';
        return $this;
    }

    /**
     * 配置选项
     * @param string $attribute 属性
     * @param string $value 值
     */
    public function props($attribute, $value)
    {
        $this->props[$attribute] = $value;
        return $this;
    }

	/**
	 * 设置选项数据
	 * @param array  $data
	 * @param string $id 下级字段
	 * @param string $parent_id 上级字段
	 * @param string $label 名称字段
	 * @param string $children 下级数组名
	 */
	public function options(array $data, $id = 'id', $parent_id = 'pid', $label = 'label', $children = 'children')
	{
		$options = Admin::tree($data, $id, $parent_id, $children);
		$this->props('value', $id);
		$this->props('label', $label);
		$this->props('children', $children);
		$this->attr('options', $options);
		return $this;
	}

    /**
     * 父子节点取消关联
     * @return $this
     */
    public function checkStrictly()
    {
        $this->props('checkStrictly', true);
        return $this;
    }

    public function bindFields($fields)
    {
        $this->fields = $fields;
        foreach ($fields as $field) {
            $this->bindAttr($field, $field);
        }
    }
    /**
     * 解析关联数据成Cascader绑定数据格式
     * @param $data
     * @return array
     */
    public function parseRelationData($data){
        $bindData = [];
        foreach ($data as $row){
            $value = [];
            foreach ($this->fields as $field){
                $value[] = $row[$field];
            }
            $value = array_filter($value);
            $bindData[] = $value;
        }
        return $bindData;
    }
    public function bind(string $name, $value = null)
    {

        if (in_array($name, $this->fields)) {
            if (!$this->initDefault) {
                $this->default     = [];
                $this->initDefault = true;
            }
            array_push($this->default, $value);
        }
        //一对多关联数据处理
        if ($this->bindAttr('relation') == $name) {
            $this->default = [];
            if(is_array($value)){
                foreach ($value as $row) {
                    $rowValue = [];
                    foreach ($this->fields as $field) {
                        if (!empty($row[$field])) {
                            $rowValue[] = $row[$field];
                        }
                    }
                    array_push($this->default, $rowValue);
                }
            }
        }
        return parent::bind($name, $value); // TODO: Change the autogenerated stub
    }

    /**
     * 多选
     * @param string $relation 关联方法
     * @return $this
     */
    public function multiple($relation = '')
    {
        if ($relation) {
            foreach ($this->fields as $field) {
                $this->removeAttrBind($field);
            }
            $this->bind($relation, []);
            $this->bindAttr('relation', $relation);
        }
        $this->props('multiple', true);
        return $this;
    }

    public function jsonSerialize()
    {
        $this->attr('props', $this->props);
        return parent::jsonSerialize(); // TODO: Change the autogenerated stub
    }
}
