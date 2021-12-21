<?php


namespace Eadmin\component\basic;

use Eadmin\component\Component;

/**
 * 描述列表
 * @method $this label(string $label) 标签文本
 * @method $this span(int $span = 1) 列的数量
 * @method $this width(string|int $width) 列的宽度，不同行相同列的宽度按最大值设定（如无 border ，宽度包含标签与内容）
 * @method $this minWidth(string|int $width) 列的最小宽度，与 width 的区别是 width 是固定的，min-width 会把剩余宽度按比例分配给设置了 min-width 的列（如无 border，宽度包含标签与内容）
 * @method $this align(string $align = 'left') 列的内容对齐方式（如无 border，对标签和内容均生效） left / center / right
 * @method $this labelAlign(string $label) 列的标签对齐方式，若不设置该项，则使用内容的对齐方式（如无 border，请使用 align 参数） left / center / right
 * @method $this className(string $name) 列的内容自定义类名
 * @method $this labelClassName(string $name) 列的标签自定义类名
 * @link https://element-plus.gitee.io/#/zh-CN/component/descriptions
 * Class Descriptions
 * @package Eadmin\component\basic
 */
class DescriptionsItem extends Component
{
	protected $name = 'ElDescriptionsItem';

}