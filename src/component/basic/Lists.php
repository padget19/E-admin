<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-06-27
 * Time: 21:15
 */

namespace Eadmin\component\basic;


use Eadmin\component\Component;

/**
 * Class Lists
 * @package Eadmin\component\basic
 */
class Lists extends Component
{
    protected $name = 'AList';
    public static function create()
    {
        return new self();
    }
    /**
     * 头部内容
     * @param $content
     * @return Lists
     */
    public function header($content){
        return $this->content($content,'header');
    }
    /**
     * 底部内容
     * @param $content
     * @return Lists
     */
    public function footer($content){
        return $this->content($content,'footer');
    }
    /**
     * 添加item
     * @param $title 标题区域
     * @param string $description 描述区域
     * @param string $avatar 头像区域
     * @return ListItem
     */
    public function item($title, $description = '', $avatar = ''){
        $item = new ListItem();
        $item->meta($title, $description, $avatar);
        $this->content($item);
        return $item;
    }
}
