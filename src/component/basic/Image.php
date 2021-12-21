<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

/**
 * 图片
 * Class Image
 * @link https://element-plus.gitee.io/#/zh-CN/component/image
 * @method $this src(string $src) 图片源，同原生
 * @method $this fit(string $fit) 确定图片如何适应容器框，同原生 object-fit    fill / contain / cover / none / scale-down
 * @method $this lazy(bool $value = true) 是否开启懒加载
 * @method $this scrollContainer(string $element) 开启懒加载后，监听 scroll 事件的容器
 * @method $this previewSrcList(array $images) 开启图片预览功能
 * @method $this zIndex(int $num) 设置图片预览的 z-index
 * @method $this referrerPolicy(string $policy) 原生 referrerPolicy
 * @method $this alt(string $alt) 原生 alt
 * @package Eadmin\component\basic
 */
class Image extends Component
{
    protected $name = 'ElImage';

    public static function create()
    {
        return new self();
    }
}