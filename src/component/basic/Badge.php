<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

/**
 * 标记
 * Class Badge
 * @link https://element-plus.gitee.io/#/zh-CN/component/badge
 * @method $this max(int $num) 最大值，超过最大值会显示 '{max}+'，要求 value 是 Number 类型
 * @method $this isDot(bool $value = true) 小圆点
 * @method $this value($value) 显示值
 * @method $this hidden(bool $value = true) 隐藏 badge
 * @method $this type(string $type) 类型    primary / success / warning / danger / info
 * @package Eadmin\component\basic
 */
class Badge extends Component
{
    protected $name = 'ElBadge';

    /**
     * 创建一个标记
     * @return Badge
     */
    public static function create()
    {
        return new self();
    }
}