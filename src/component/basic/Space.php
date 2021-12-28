<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

/**
 * 间距
 * Class Space
 * @link https://element-plus.gitee.io/#/zh-CN/component/space
 * @method $this alignment(string $type) 对齐的方式    align-items
 * @method $this class($class) 类名 string / Array<Object | String> / Object
 * @method $this direction(string $direction) 排列的方向 vertical / horizontal
 * @method $this prefixCls(string $prefix) 给 space-items 的类名前缀    el-space
 * @method $this style($style) 额外样式 string / Array<Object | String> / Object
 * @method $this spacer($spacer) 间隔符    string / number / VNode    -
 * @method $this size($size) 间隔大小    string / number / [number, number]
 * @method $this wrap(bool $value = true) 设置是否自动折行
 * @package Eadmin\component\basic
 */
class Space extends Component
{
    protected $name = 'ElSpace';
    public function __construct($content)
    {
        parent::__construct();
        if (!empty($content) || is_numeric($content)) {
            $this->content($content);
        }
    }

    public static function create($content = '')
    {
        return new self($content);
    }
}
