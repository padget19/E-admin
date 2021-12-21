<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;
use Eadmin\constant\Style;

/**
 * 快捷跳转卡片
 * Class QuickLink
 * @package Eadmin\component\basic
 */
class QuickLink extends Component
{
    protected $name = 'html';

    public function __construct($text, $icon, $color)
    {
        parent::__construct();
        $this->attr('data-tag', 'div');
        $this->content(
            Card::create([
                "<i class='{$icon}' style='font-size: 32px;color: {$color}'></i>",
                Html::div()->content($text)->style(['marginTop'=>'8px','color'=>'#515a6e','font-size'=>'14px'])
            ])->shadow('hover')->bodyStyle(['padding'=>'16px']+Style::FLEX + Style::FLEX_DIRECTION_COLUMN + Style::FLEX_ITEMS_CENTER)
        );
    }

    public static function create($text, $icon, $color = '#409eff')
    {
        return new static($text, $icon, $color);
    }
}
