<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-12-27
 * Time: 21:28
 */

namespace Eadmin\component\layout;


use Eadmin\component\Component;

/**
 * Class Content
 * @package Eadmin\component\layout
 * @method $this direction(string $direction) 子元素的排列方向 horizontal / vertical
 */
class Content extends Component
{
    protected $name = 'ElContainer';

    public function __construct()
    {
        parent::__construct();
        $this->direction('vertical');
    }

    public static function create()
    {
        return new self;
    }

    /**
     * 设置标题
     * @param string $title
     * @return $this
     */
    public function title(string $title)
    {
        $this->bind('eadmin_title', $title);
        return $this;
    }
    /**
     * 添加一行
     * @param mixed $content 内容
     * @param int $span 栅格占据的列数,默认24
     * @return Row
     */
    public function row($content, $span = 24)
    {
        $row = new Row();
        if ($content instanceof \Closure) {
            call_user_func($content, $row);
        } else {
            $row->column($content, $span);
        }
        $this->content($row);
        return $row;
    }
}
