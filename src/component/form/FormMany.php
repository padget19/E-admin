<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-01-01
 * Time: 09:45
 */

namespace Eadmin\component\form;


use Eadmin\component\form\Field;

/**
 *
 * Class FormMany
 * @link https://element-plus.gitee.io/#/zh-CN/component/form
 * @package Eadmin\component\form\field
 * @property Form $form
 * @method $this table(bool $value=true) 表格模式
 * @method $this limit(int $value) 限制数量
 */
class FormMany extends Field
{
    protected $name = 'EadminManyItem';
   
}
