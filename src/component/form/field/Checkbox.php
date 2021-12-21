<?php


namespace Eadmin\component\form\field;


use Eadmin\component\Component;


/**
 * 多选框
 * Class Checkbox
 * @link https://element-plus.gitee.io/#/zh-CN/component/checkbox
 * @method $this trueLabel(string $value) 选中时的值
 * @method $this falseLabel(string $value) 没有选中时的值
 * @method $this border(bool $border) 是否显示边框
 * @method $this size(string $size) Checkbox 的尺寸，仅在 border 为真时有效 medium / small / mini
 * @method $this checked(bool $check) 当前是否勾选
 * @method $this indeterminate(bool $value = true) 设置 indeterminate 状态，只负责样式控制
 * @package Eadmin\component\form\field
 */
class Checkbox extends Component
{
    protected $name = 'ElCheckbox';
}
