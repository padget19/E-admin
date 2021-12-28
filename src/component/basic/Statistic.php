<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;
use Eadmin\constant\Style;

class Statistic extends Component
{
    protected $name = 'html';

	/**
	 * 统计卡片
	 * @param string $title    标题
	 * @param string $value    值
	 * @param string $icon     图标
	 * @param string $bgColor1 渐变背景颜色1
	 * @param string $bgColor2 渐变背景颜色2
	 */
	public function __construct($title, $value, $icon, $bgColor1, $bgColor2)
	{
		$this->content(
			Html::create([
				Html::create(
					Html::create()
						->tag('i')
						->attr('class', $icon)
						->style([
							'fontSize' => '50px',
						])
				)
					->style([
						'margin' => '0 10px',
					])
					->tag('div'),
				Html::create([
					Html::create($title)
						->tag('div')
						->style([
							'fontSize'     => '14px',
							'marginBottom' => '4px',
						]),
					Html::create($value)
						->tag('div')
						->style([
							'fontSize'   => '24px',
							'lineHeight' => '24px',
						]),
				])->tag('div'),
			])
				->style([
					'display'      => 'flex',
					'alignItems'   => 'center',
					'background'   => "linear-gradient(145deg, {$bgColor1} 0%, {$bgColor2} 100%)",
					'color'        => '#ffffff',
					'borderRadius' => '5px',
					'padding'      => '15px 10px',
				])
				->tag('div')
		);
	}

	/**
	 * 统计卡片
	 * @param string $title    标题
	 * @param string $value    值
	 * @param string $icon     图标
	 * @param string $bgColor1 渐变背景色1
	 * @param string $bgColor2 渐变背景色2
	 * @return static
	 */
	public static function create($title, $value, $icon = 'el-icon-collection', $bgColor1 = '#884add', $bgColor2 = '#c08afa')
	{
		return new static($title, $value, $icon, $bgColor1, $bgColor2);
	}

    /**
     * 统计卡片
     * @param string $text 文本
     * @param string|int $value 值
     * @param string $icon 图标
     * @param string $color 颜色
     * @return Card
     */
	public static function card($text,$value,$icon,$color='#409eff'){
        return Card::create([
            Html::create()
                ->tag('i')
                ->attr('class', $icon)
                ->style([
                    'fontSize' => '50px',
                    'color'=>$color,
                ]),
            Html::create([
                Html::create($text)
                    ->tag('div')
                    ->style([
                        'fontSize'     => '14px',
                        'marginBottom' => '4px',
                    ]),
                Html::create($value)
                    ->tag('div')
                    ->style([
                        'fontSize'   => '24px',
                        'lineHeight' => '24px',
                    ]),
            ])->tag('div')->style(['marginLeft'=>'10px'])
        ])->bodyStyle(Style::FLEX_CENTER);
    }
}
