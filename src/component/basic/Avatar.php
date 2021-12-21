<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

/**
 * 头像
 * Class Avatar
 * @link https://element-plus.gitee.io/#/zh-CN/component/avatar
 * @method $this icon(string $icon) 设置头像的图标类型，参考 Icon 组件
 * @method $this size(string $size) 设置头像的大小 number / large / medium / small
 * @method $this shape(string $string) 设置头像的形状    circle / square
 * @method $this src(string $src) 图片头像的资源地址
 * @method $this srcSet(string $set) 以逗号分隔的一个或多个字符串列表表明一系列用户代理使用的可能的图像
 * @method $this alt(string $alt) 描述图像的替换文本
 * @method $this fit(string $fit) 当展示类型为图片的时候，设置图片如何适应容器框 fill / contain / cover / none / scale-down
 * @package Eadmin\component\basic
 */
class Avatar extends Component
{
    protected $name = 'ElAvatar';

    public static function create()
    {
        return new self();
    }
}