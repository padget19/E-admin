<?php


namespace Eadmin\component\form;


use Eadmin\component\basic\Html;
use Eadmin\component\basic\Tip;
use Eadmin\component\Component;
use Eadmin\component\form\field\Input;
use Eadmin\form\traits\Validator;
use Eadmin\form\traits\WhenForm;
use think\helper\Str;

/**
 * Class Field
 * @package Eadmin\component\form
 * @method $this disabled(bool $vlaue = true) 禁用
 * @property FormItem $formItem
 */
abstract class Field extends Component
{
    use WhenForm, Validator;

    protected $name = 'html';
    protected $default = null;
    protected $value = null;
    protected $formItem;
    public $md = null;

    public function __construct($field = null, $value = '')
    {
        parent::__construct();
        $this->attr('data-tag', 'component');
        $this->bindValue($value, 'modelValue', $field);
    }

    /**
     * 占列
     * @param int $span
     * @return $this
     */
    public function md(int $span = 24)
    {
        $this->md = $span;
        if ($this->formItem) {
            $this->formItem->md($span);
        }

        return $this;
    }

    /**
     * 表单新增更新验证规则
     * @Author: rocky
     * 2019/8/9 10:50
     * @param array $rule 验证规则
     */
    public function rule(array $rule)
    {
        $this->formItem->rules($rule);
        return $this;
    }

    /**
     * 表单新增验证规则
     * @Author: rocky
     * 2019/8/9 10:50
     * @param array $rule 验证规则
     */
    public function createRule(array $rule)
    {
        $this->formItem->rules($rule, 1);
        return $this;
    }

    /**
     * 表单更新验证规则
     * @Author: rocky
     * 2019/8/9 10:50
     * @param array $rule 验证规则
     */
    public function updateRule(array $rule)
    {
        $this->formItem->rules($rule, 2);
        return $this;
    }

    /**
     * 是否必填
     * @return $this
     */
    public function required()
    {

        $this->formItem->required();
        return $this;
    }

    public function setFormItem(FormItem $formItem)
    {
        $this->formItem = $formItem;
    }

    /**
     * @return FormItem
     */
    public function getFormItem(){
        return $this->formItem;
    }
    /**
     * 设置缺省默认值
     * @param mixed $value
     */
    public function default($value)
    {
        $this->default = $value;
        return $this;
    }

    public function append($content)
    {
        $this->formItem->content($content);
        return $this;
    }

    /**
     * 帮助内容
     * @param mixed $content 内容
     * @return $this
     */
    public function help($content)
    {
        $this->formItem->content(Html::create($content)->attr('style', ['fontSize' => '12px'])->tag('div'));
        return $this;
    }

	/**
	 * icon形式的帮助内容
	 * @param string       $content 提示的信息
	 * @param string $icon 图标样式
	 * @param string $placement Tooltip 的出现位置 top/top-start/top-end/bottom/bottom-start/bottom-end/left/left-start/left-end/right/right-start/right-end
	 * @param string $effect 主题  dark / light
	 * @return $this
	 */
	public function tip(string $content, string $icon = 'el-icon-warning-outline', string $placement = 'top-start', string $effect = 'light')
	{
		$this->formItem->content(Tip::create(
			Html::create()
				->tag('i')
				->attr('class', $icon)
		)
			->content($content)
			->effect($effect)
			->placement($placement),'label');
		return $this;
	}

    /**
     * 获取缺省默认值
     * @return |null
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * 获取设置固定值
     * @return |null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * 设置值
     * @param mixed $value
     */
    public function value($value)
    {
        $this->value = $value;
        $field = $this->bindAttr('modelValue');
        $this->bind($field, $value);
        return $this;
    }

    /**
     * 创建
     * @param string $field 字段
     * @param mixed $value 值
     * @return static
     */
    public static function create($field = null, $value = '')
    {
        return new static($field, $value);
    }
    /**
     * 双向绑定值
     * @param mixed $value 值
     * @param string $attr 属性
     * @param string $field 字段
     */
    public function bindValue($value = '', $attr = 'modelValue', $field = null)
    {
        empty($field) ? $field = Str::random(30, 3) : $field;
        $this->bind($field, $value);
        $this->bindAttr($attr, $field, true);
        return $this;
    }
}
