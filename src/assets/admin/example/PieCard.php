<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-06-22
 * Time: 21:32
 */

namespace app\admin\example;


use Eadmin\chart\echart\LineChart;
use Eadmin\chart\echart\PieChart;
use Eadmin\component\basic\Card;
use Eadmin\component\basic\Html;
use Eadmin\component\Component;
use Eadmin\component\form\field\Select;
use Eadmin\component\WatchComponent;
use Eadmin\constant\Style;

class PieCard extends WatchComponent
{

    function component(): Component
    {
        return Select::create(null, 7)
            ->options([
                7 => 'Last 7 Days',
                28 => 'Last 28 Days',
                30 => 'Last Month',
                365 => 'Last Year',
            ])
            ->size('mini')
            ->clearable(false)
            ->filterable(false)
            ->style(['width' => '120px', 'marginRight' => '10px']);
    }

    function watch($value = null)
    {
        $data[] = [
            'name' => '淘宝',
            'value' => rand(10, 5000)
        ];
        $data[] = [
            'name' => '京东',
            'value' => rand(10, 5000)
        ];
        $data[] = [
            'name' => '美团',
            'value' => rand(10, 5000)
        ];
        switch ($value) {
            case 7:
                $this->content($this->render(rand(1000, 5000), $data));
                break;
            case 28:
                $this->content($this->render(rand(1000, 5000), $data));
                break;
            case 30:
                $this->content($this->render(rand(1000, 5000), $data));
                break;
            case 365:
                $this->content($this->render(rand(1000, 5000), $data));
                break;
        }
        return $this;
    }

    public function render($num, $data)
    {
        $echart = new PieChart('98px', '180px');
        $echart->setOptions([
            'legend' => [
                'orient' => 'vertical',
                'left' => 'right'
            ]
        ]);
        $echart->series('', $data, [
            'label' => '',
            'center' => ['30%', '40%'],
            'radius' => ['40%', '80%'],
        ]);
        return Card::create([
            Html::create([
                Html::create('电商')->style(['marginLeft' => '20px', 'color' => '#2c2c2c', 'fontSize' => '16px']),
                $this->component,
            ])->tag('div')->style(Style::FLEX_BETWEEN_CENTER + ['padding' => '10px 0']),
            Html::create([
                Html::create([
                    Html::create($num)->tag('div')->style(['fontSize' => '30px']),
                  //  Html::create('total')->tag('div'),
                ])->style(['marginLeft' => '20px']),
                Html::create($echart)->style(['marginRight' => '15px']),
            ])->tag('div')->style(Style::FLEX_BETWEEN_CENTER),
        ])->bodyStyle(['padding' => '0']);
    }
}