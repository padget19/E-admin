<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-12-27
 * Time: 21:25
 */

namespace Eadmin\component\layout;


use Eadmin\component\Component;

class Row extends Component
{
    protected $name = 'ElRow';
    /**
     * 栅格间隔
     * @param int $value
     * @return Row
     */
    public function gutter(int $value)
    {
        return $this->attr(__FUNCTION__, $value);
    }
    /**
     * /**
     * 添加列
     * @param \Closure|String $content 内容
     * @param int $span 栅格占据的列数,占满一行24,默认24
     * @return Column
     */
    public function column($content,int $span = 24){
        $column = new Column();
        $column->span($span);
        if($content instanceof \Closure){
            call_user_func($content,$column);
        }else{
            $column->content($content);
        }
        $this->content($column);
        return $column;

    }
}
