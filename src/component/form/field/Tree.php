<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-01-22
 * Time: 21:56
 */

namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

/**
 * Class Tree
 * @package Eadmin\component\basic
 * @method $this data(array $data) 展示数据
 * @method $this props(array $data) 配置选项
 * @method $this nodeKey(string $value) 每个树节点用来作为唯一标识的属性，整棵树应该是唯一的
 * @method $this defaultExpandAll(bool $bool = true) 是否默认展开所有节点
 * @method $this highlightCurrent(bool $bool = true) 是否高亮当前选中节点
 * @method $this showCheckbox(bool $bool = true) 节点是否可被选择
 * @method $this horizontal(bool $bool = true) 横向
 */
class Tree extends Field
{
    protected $name = 'EadminTree';

    public function __construct($field = null,$value = '')
    {
        $this->nodeKey('id');
        parent::__construct($field, $value);
    }

}
