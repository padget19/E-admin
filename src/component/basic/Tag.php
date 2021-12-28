<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

/**
 * 标签
 * Class Tag
 * @link https://element-plus.gitee.io/#/zh-CN/component/tag
 * @method $this type(string $type) 类型 success / info / warning / danger
 * @method $this closable(bool $value = true) 是否可关闭
 * @method $this disableTransitions(bool $value = true) 是否禁用渐变动画
 * @method $this hit(bool $value = true) 是否有边框描边
 * @method $this color(string $color) 背景色
 * @method $this size(string $color) 尺寸    medium / small / mini
 * @method $this effect(string $color) 主题  dark / light / plain
 * @package Eadmin\component\basic
 */
class Tag extends Component
{
    protected $name = 'ElTag';

    public function __construct($content)
    {
        parent::__construct();
        if (!empty($content) || is_numeric($content)) {
            $this->content($content);
        }
    }

    /**
     * 创建
     * @param string $content 标签内容
     * @return static
     */
    public static function create($content = '')
    {
        return new static($content);
    }
}
