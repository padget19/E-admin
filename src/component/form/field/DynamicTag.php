<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

/**
 * 动态标签
 * Class DynamicTag
 * @package Eadmin\component\form\field
 * @method $this text(string $text) 按钮文本
 */
class DynamicTag extends Field
{
    protected $name = 'EadminTag';
    public function __construct($field = null, $value = '')
    {
        parent::__construct($field, $value);
    }
}
