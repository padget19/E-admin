<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

/**
 * 时间线
 * Class TimeLine
 * @link https://element-plus.gitee.io/#/zh-CN/component/timeline
 * @package Eadmin\component\basic
 */
class TimeLine extends Component
{
    protected $name = 'ElTimeline';
    public static function create()
    {
        return new self();
    }

    /**
     * @param $content
     * @return TimeLineItem
     */
    public function item($content){
       $item =  new TimeLineItem();
       $item->content($content);
       $this->content($item);
       return $item;
    }
}
