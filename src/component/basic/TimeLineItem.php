<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

/**
 * 时间线
 * Class TimeLine
 * @link https://element-plus.gitee.io/#/zh-CN/component/timeline
 * @method $this timestamp(string $time)    时间戳
 * @method $this hideTimestamp(bool $value = true) 是否隐藏时间戳
 * @method $this placement(string $placement) 时间戳位置  top / bottom
 * @method $this type(string $type) 节点类型 primary / success / warning / danger / info
 * @method $this color(string $color) 节点颜色 hsl / hsv / hex / rgb
 * @method $this size(string $size) 节点尺寸 normal / large
 * @method $this icon(string $icon) 节点图标
 * @package Eadmin\component\basic
 */
class TimeLineItem extends Component
{
    protected $name = 'ElTimelineItem';
}
