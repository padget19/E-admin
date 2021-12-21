<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

class Statistic extends Component
{
    protected $name = 'html';

    /**
     * 统计卡片
     * @param string $title 标题
     * @param string $value 值
     * @param string $icon 图标
     * @param string $bgColor1 渐变背景颜色1
     * @param string $bgColor2 渐变背景颜色2
     */
    public function __construct($title, $value, $icon, $bgColor1, $bgColor2)
    {
        $html = <<<HTML
        <div style="display: flex;align-items: center; background:  linear-gradient(145deg, {$bgColor1} 0%, {$bgColor2} 100%);color: #ffffff;border-radius: 5px;padding:15px 10px">
        <div style="margin:0 10px"><i class="{$icon}" style="font-size:50px;"></i></div>
        <div>
        <div style="font-size: 14px;margin-bottom: 4px;">{$title}</div><div style="font-size: 24px;line-height: 24px">{$value}</div>
        </div>
</div>
HTML;
        $this->content($html);
    }

    /**
     * 统计卡片
     * @param string $title 标题
     * @param string $value 值
     * @param string $icon 图标
     * @param string $bgColor1 渐变背景色1
     * @param string $bgColor2 渐变背景色2
     * @return static
     */
    public static function create($title, $value, $icon, $bgColor1, $bgColor2)
    {
        return new static($title, $value, $icon, $bgColor1, $bgColor2);
    }
}
