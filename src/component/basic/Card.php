<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

/**
 * 卡片
 * Class Card
 * @link https://element-plus.gitee.io/#/zh-CN/component/card
 * @package Eadmin\component\basic
 * @method $this shadow(string $shadow) always / hover / never
 * @method $this bodyStyle(array $style) 设置 body 的样式
 */
class Card extends Component
{
    protected $name = 'ElCard';

    public function __construct($content)
    {
        parent::__construct();
        if (!empty($content)) {
            $this->content($content);
        }
    }

    /**
     * 创建卡片
     * @param string $content 内容
     * @return Card
     */
    public static function create($content = '')
    {
        return new self($content);
    }

    /**
     * 头部内容
     * @param string $content 内容
     */
    public function header($content)
    {
        return $this->content($content, 'header');
    }
}
