<?php


namespace Eadmin\component\basic;

use Eadmin\component\Component;
use think\helper\Str;

/**
 * Class Tooltip
 * @package Eadmin\component\basic
 * @method $this effect(string $color) 主题  dark / light
 * @method $this appendToBody(bool $value = true) 决定 popper 是否传送到 document.body 下
 * @method $this disabled(bool $value = true) Tooltip 是否可用
 * @method $this offset(int $value) 出现位置的偏移量
 * @method $this placement(string $value) Tooltip 的出现位置 top/top-start/top-end/bottom/bottom-start/bottom-end/left/left-start/left-end/right/right-start/right-end
 */
class Tip extends Component
{
    protected $name = 'ElTooltip';

    public function __construct($content)
    {
        parent::__construct();
        $this->content(Html::create($content), 'default');
    }

    public static function create($content = '')
    {
        return new static($content);
    }

    public function content($content, $name = 'content')
    {
        return parent::content($content, $name); // TODO: Change the autogenerated stub
    }
}
