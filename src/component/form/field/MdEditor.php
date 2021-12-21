<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-10-15
 * Time: 23:43
 */

namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

/**
 * Class MdEditor
 * @package Eadmin\component\form\field
 * @link https://ckang1229.gitee.io/vue-markdown-editor/zh/api.html#props
 * @method $this height(string $height) 高度 500px
 */
class MdEditor extends Field
{
    protected $name = 'v-md-editor';
    public function __construct($field = null, string $value = '')
    {
        parent::__construct($field, $value);
        $this->height('500px');
    }
}