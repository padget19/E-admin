<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

/**
 * 颜色选择器
 * Class Color
 * @link https://element-plus.gitee.io/#/zh-CN/component/color-picker
 * @method $this size(string $size) 尺寸 medium / small / mini
 * @method $this showAlpha(bool $value = true) 是否支持透明度选择
 * @method $this colorFormat(string $format) 写入 v-model 的颜色的格式     hsl / hsv / hex / rgb
 * @method $this popperClass(string $class) ColorPicker 下拉框的类名
 * @method $this predefine(array $color) 预定义颜色
 * @package Eadmin\component\form\field
 */
class Color extends Field
{
    protected $name = 'ElColorPicker';
}
