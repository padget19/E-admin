<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-12-27
 * Time: 21:04
 */

namespace Eadmin\component\form\field;

use Eadmin\component\form\Field;

/**
 * 输入框
 * Class Input
 * @link https://element-plus.gitee.io/#/zh-CN/component/input
 * @method $this size(string $value) 输入框尺寸，只在 type!="textarea" 时有效 medium / small / mini
 * @method $this type(string $value) 输入框类型  text / textarea / hidden / password / number
 * @method $this showWordLimit(bool $value = true) 是否显示输入字数统计，只在 type = "text" 或 type = "textarea" 时有效
 * @method $this placeholder(string $text) 输入框占位文本
 * @method $this clearable(bool $value = true) 是否可清空
 * @method $this showPassword(bool $value = true) 是否显示切换密码图标
 * @method $this prefixIcon(string $icon) 输入框头部图标
 * @method $this suffixIcon(string $icon) 输入框尾部图标
 * @method $this rows(int $row) 输入框行数，只对 type = "textarea" 有效
 * @method $this autosize($row) 自适应内容高度，只对 type = "textarea" 有效，可传入对象，如，{
minRows: 2, maxRows: 6
}
 * @method $this autocomplete(string $complete) 原生属性，自动补全 on / off
 * @method $this resize(string $resize) 控制是否能被用户缩放 none / both / horizontal / vertical
 * @method $this readonly(bool $read = false) 原生属性，是否只读
 * @method $this max($num) 原生属性，设置最大值
 * @method $this min($num) 原生属性，设置最小值
 * @method $this step($step) 原生属性，设置输入字段的合法数字间隔
 * @method $this minlength(int $num) 原生属性，最小输入长度
 * @method $this maxlength(int $num) 原生属性，最大输入长度
 * @method $this autofocus(bool $focus) 原生属性，自动获取焦点 true / false
 * @method $this form(string $form) 原生属性
 * @method $this tabindex(string $index) 输入框的tabindex
 * @method $this validateEvent(bool $validate) 输入时是否触发表单的校验
 * @package Eadmin\component\form\field
 */
class Input extends Field
{
    protected $name = 'ElInput';

    public function __construct($field = null, $value = '')
    {
        parent::__construct($field, $value);
        $this->clearable();
    }

    /**
     * 输入框头部内容，只对 type="text" 有效
     * @param mixed $content
     * @return Input
     */
    public function prefix($content)
    {
        return $this->content($content, 'prefix');
    }

    /**
     * 输入框前置内容，只对 type="text" 有效
     * @param mixed $content
     * @return Input
     */
    public function suffix($content)
    {
        return $this->content($content, 'suffix');
    }

    /**
     * 输入框前置内容只对 type="text" 有效
     * @param mixed $content
     * @return Input
     */
    public function prepend($content)
    {
        return $this->content($content, 'prepend');
    }

    /**
     * 输入框后置内容只对 type="text" 有效
     * @param mixed $content
     * @return Input
     */
    public function append($content)
    {
        return $this->content($content, 'append');
    }
}
