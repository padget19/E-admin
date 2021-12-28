<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

/**
 * Class Html
 * @package Eadmin\component\basic
 * @method static Html p() p标签
 * @method static Html div() div标签
 * @method static Html strong() div标签
 */
class Html extends Component
{
    protected $name = 'html';

    public function __construct($content = '')
    {
        parent::__construct();
        $this->tag('span');
        if (!empty($content) || is_numeric($content)) {
            $this->content($content);
        }
    }

    /**
     * 自定义元素标签
     * @param string $tag 元素标签
     */
    public function tag($tag)
    {
        $this->attr('data-tag', $tag);
        return $this;
    }
    public static function __callStatic($name, $arguments)
    {
        $self =  new static();
        $self->tag($name);
        return $self;
    }
    public static function code($content){
        $self =  new static();
        $self->name = 'highlight';
        $self->attr('code',$content);
        return $self;
    }
    public static function create($content = '')
    {
        return new static($content);
    }
}
