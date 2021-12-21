<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-12-27
 * Time: 21:29
 */

namespace Eadmin\component\layout;


use Eadmin\component\Component;

class Column extends Component
{
    protected $name = 'ElCol';

    /**
     * 添加一行
     * @param mixed $content
     * @return Row
     */
    public function row($content)
    {
        $row = new Row();
        if ($content instanceof \Closure) {
            call_user_func($content, $row);
        } else {
            $row->column($content);
        }
        $this->content($row);
        return $row;
    }

    /**
     * 栅格向左移动格数
     * @param int $value
     * @return $this
     */
    public function pull(int $value)
    {
        return $this->attr(__FUNCTION__, $value);
    }

    /**
     * 栅格向右移动格数
     * @param int $value
     * @return $this
     */
    public function push(int $value)
    {
        return $this->attr(__FUNCTION__, $value);
    }

    /**
     * 栅格左侧的间隔格数
     * @param int $value
     * @return $this
     */
    public function offset(int $value)
    {
        return $this->attr(__FUNCTION__, $value);
    }

    /**
     * 栅格占据的列数
     * @param int $value
     * @return $this
     */
    public function span(int $value)
    {
        $this->attr('md', $value);
        $sm = $value + $value / 2;
        if ($sm > 24) {
            $sm = 24;
        }
        $this->attr('sm', $sm);
        $xs = $value * 4;
        if ($xs > 24) {
            $xs = 24;
        }
        $this->attr('xs', $xs);
        return $this->attr(__FUNCTION__, $value);
    }
}
