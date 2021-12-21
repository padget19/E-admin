<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

/**
 * 滑块
 * Class Slider
 * @link https://element-plus.gitee.io/#/zh-CN/component/switch
 * @method $this min(int $num) 最小值
 * @method $this max(int $num) 最大值
 * @method $this step(int $num) 步长
 * @method $this showInput(bool $value = true) 是否显示输入框，仅在非范围选择时有效
 * @method $this showInputControls(bool $value = true) 在显示输入框的情况下，是否显示输入框的控制按钮
 * @method $this inputSize(string $size) 输入框的尺寸  large / medium / small / mini
 * @method $this showStops(bool $value = true) 是否显示间断点
 * @method $this showTooltip(bool $value = true) 是否显示 tooltip
 * @method $this range(bool $value = true) 是否为范围选择
 * @method $this vertical(bool $value = true) 是否竖向模式
 * @method $this height(string $height) Slider 高度，竖向模式时必填
 * @method $this label(string $label) 屏幕阅读器标签
 * @method $this debounce(int $num) 输入时的去抖延迟，毫秒，仅在show-input等于true时有效
 * @method $this tooltipClass(string $class) tooltip 的自定义类名
 * @method $this marks($object) tooltip 标记， key 的类型必须为 number 且取值在闭区间 [min, max] 内，每个标记可以单独设置样式
 * @package Eadmin\component\form\field
 */
class Slider extends Field
{
    protected $name = 'ElSlider';

    public function __construct($field = null, $value = '')
    {
        if (empty($value)) {
            $value = 0;
        }
        parent::__construct($field, $value);
    }
}
