<?php


namespace Eadmin\component\basic;



use Eadmin\component\form\Field;

class Collapse extends Field
{
    protected $name = 'ElCollapse';

    /**
     * item内容
     * @param string $title 标题
     * @param string $content 内容
     * @return $this
     */
    public function item($title,$content){
        $item = new CollapseItem();
        $item->content($title,'title');
        $item->content($content);
        $this->content($item);
        return $this;
    }
}
