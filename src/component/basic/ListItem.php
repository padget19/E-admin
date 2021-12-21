<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-06-27
 * Time: 21:16
 */

namespace Eadmin\component\basic;


use Eadmin\component\Component;

class ListItem extends Component
{
    protected $name = 'AListItem';

    /**
     * 操作组
     * @param $content 内容
     * @return $this
     */
    public function actions($content){
        $this->content($content,'actions');
        return $this;
    }
    /**
     * 额外内容, 通常用在 itemLayout 为 vertical 的情况下, 展示右侧内容; horizontal 展示在列表元素最右侧
     * @param $content 内容
     * @return $this
     */
    public function extra($content){
        $this->content($content,'extra');
        return $this;
    }

    /**
     * 
     * @param $title 标题区域
     * @param string $description 描述区域
     * @param string $avatar 头像区域
     * @return $this
     */
    public function meta($title, $description = '', $avatar = '')
    {
        $meta = new ListItemMeta();
        $meta->content($title, 'title');
        if ($description) {
            $meta->content($description, 'description');
        }
        if ($avatar) {
            $meta->content($avatar, 'avatar');
        }
        $this->content($meta);
        return $this;
    }
}