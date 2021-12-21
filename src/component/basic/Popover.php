<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-02-26
 * Time: 23:57
 */

namespace Eadmin\component\basic;

/**
 * 弹出框
 * Class Popover
 * @package Eadmin\component\basic
 * @method $this trigger(string $string)    触发方式  click/focus/hover/manual
 * @method $this width(int|string $string) 宽度 最小宽度 150px
 * @method $this placement(int|string $string) 出现位置 top/top-start/top-end/bottom/bottom-start/bottom-end/left/left-start/left-end/right/right-start/right-end
 * @method $this offset(int $number) 出现位置的偏移量
 */
class Popover extends \Eadmin\component\Component
{
    protected $name = 'ElPopover';

    public function __construct($content)
    {
        $this->content(Html::create($content), 'reference');
    }

    /**
     * 设置标题
     * @param string $title 标题内容
     * @return Popover|void
     */
    public function title($title)
    {
        $this->attr('title', $title);
    }

    public static function create($content)
    {
        return new self($content);
    }
}