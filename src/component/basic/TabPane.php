<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-01-06
 * Time: 23:32
 */

namespace Eadmin\component\basic;


use Eadmin\component\Component;

/**
 * Class TabPane
 * @package Eadmin\component\basic
 * @method $this stretch(bool $value = true) 标签的宽度是否自撑开
 * @method $this closable(bool $value = true) 标签是否可关闭
 * @method $this lazy(bool $value = true) 标签是否延迟渲染
 * @method $this label(string $label) 选项卡标题
 * @method $this name(string $name) 与选项卡绑定值 value 对应的标识符，表示选项卡别名
 */
class TabPane extends Component
{
    protected $name = 'ElTabPane';
}