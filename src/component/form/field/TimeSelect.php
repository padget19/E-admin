<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

/**
 * 时间选择器
 * Class TimeSelect
 * @link https://element-plus.gitee.io/#/zh-CN/component/time-select
 * @method $this editable(bool $value = true) 文本框可输入
 * @method $this clearable(bool $value = true) 是否显示清除按钮
 * @method $this size(string $size) 输入框尺寸    medium / small / mini
 * @method $this placeholder(string $text) 非范围选择时的占位内容
 * @method $this prefixIcon(string $icon) 自定义头部图标的类名
 * @method $this clearIcon(string $icon) 自定义清空图标的类名
 * @method $this start(string $time) 开始时间 09:00
 * @method $this end(string $time) 结束时间 18:00
 * @method $this step(string $time) 间隔时间
 * @method $this minTime(string $time) 最小时间，小于该时间的时间段将被禁用 00:00
 * @method $this maxTime(string $time) 最大时间，大于该时间的时间段将被禁用 00:00
 * @package Eadmin\component\form\field
 */
class TimeSelect extends Field
{
    protected $name = 'ElTimeSelect';
}