<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

/**
 * 进度条
 * Class Process
 * @link https://element-plus.gitee.io/#/zh-CN/component/tag
 * @method $this percentage(int $num) 百分比（必填） 0-100
 * @method $this type(string $type) 进度条类型  line / circle / dashboard
 * @method $this strokeWidth(int $num) 进度条的宽度，单位 px
 * @method $this textInside(bool $value = true) 进度条显示文字内置在进度条内（只在 type = line 时可用）
 * @method $this status(string $status) 进度条当前状态  success / exception / warning
 * @method $this color($color) 进度条背景色（会覆盖 status 状态颜色
 * @method $this width(int $num) 环形进度条画布宽度（只在 type 为 circle 或 dashboard 时可用）
 * @method $this showText(bool $value = true) 是否显示进度条文字内容
 * @method $this strokeLinecap(string $cap) circle/dashboard 类型路径两端的形状 butt / round / square
 * @package Eadmin\component\basic
 */
class Process extends Component
{
    protected $name = 'ElProgress';
    public static function create()
    {
        return new self();
    }
}
