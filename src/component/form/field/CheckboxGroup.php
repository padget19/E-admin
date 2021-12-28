<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;
use think\helper\Str;

/**
 * 多选框组
 * Class CheckboxGroup
 * @link https://element-plus.gitee.io/#/zh-CN/component/checkbox
 * @method $this size(string $size) 多选框组尺寸，仅对按钮形式的 Checkbox 或带有边框的 Checkbox 有效 medium / small / mini
 * @method $this min(int $num) 可被勾选的 checkbox 的最小数量
 * @method $this max(int $num) 可被勾选的 checkbox 的最大数量
 * @method $this checkAll($bool = true) 默认全选
 * @method $this horizontal($bool = true) 竖排方向
 * @method $this onCheckAll($bool = true) 开启全选
 * @method $this textColor(string $color) 按钮形式的 Checkbox 激活时的文本颜色
 * @method $this fill(string $color) 按钮形式的 Checkbox 激活时的填充色和边框色
 * @package Eadmin\component\form\field
 */
class CheckboxGroup extends Field
{
    protected $name = 'EadminCheckboxGroup';
    //禁用数据
    protected $disabledData = [];

    public function __construct($field = null, $value = [])
    {
        if (empty($value)) {
            $value = [];
        }
        $this->default = $value;
        parent::__construct($field, $value);
    }

    /**
     * 禁用选项数据
     * @param array $data 禁用数据
     */
    public function disabledData(array $data)
    {
        $this->disabledData = $data;
        return $this;
    }

    /**
     * 设置选项数据
     * @param array $data 选项数据
     * @param bool $buttonTheme 是否按钮样式
     * @return $this
     */
    public function options(array $data, bool $buttonTheme = false)
    {
        $options = [];
        foreach ($data as $value => $label) {
            if (in_array($value, $this->disabledData)) {
                $disabled = true;
            } else {
                $disabled = false;
            }
            $options[] = [
                'value' => $value,
                'label' => $label,
                'disabled' => $disabled,
            ];
        }
        if ($buttonTheme) {
            $checkbox = new CheckboxButton;
        } else {
            $checkbox = new Checkbox;
        }
        $mapField = Str::random(30, 3);
        $this->bindValue($options, 'options', $mapField);
        if ($this->formItem) {
            $this->formItem->form()->except([$mapField]);
        }

        if ($this->formItem && empty($this->formItem->form()->manyRelation())) {
            $mapField = $this->formItem->form()->bindAttr('model') . '.' . $mapField;
        }
        $checkboxOption = $checkbox
            ->map($options, $mapField)
            ->mapAttr('label', 'value')
            ->mapAttr('key', 'value')
            ->mapAttr('slotDefault', 'label')
            ->mapAttr('disabled', 'disabled');
        return $this->content($checkboxOption);
    }
}
