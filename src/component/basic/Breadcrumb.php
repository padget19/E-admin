<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

/**
 * 面包屑
 * Class Breadcrumb
 * @package Eadmin\component\basic
 * @method $this separator(string $value)  分隔符
 */
class Breadcrumb extends Component
{
    protected $name = 'ElBreadcrumb';
    public static function create()
    {
        return new self();
    }
    public function item($content,$to=''){
        $item = new BreadcrumbItem();
        $item->content($content);
        if(!empty($to)){
            $item->attr('to',$to);
        }
        $this->content($item);
        return $this;
    }
}
