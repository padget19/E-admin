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
use Eadmin\component\basic\Process;
use Eadmin\component\Component;
use Eadmin\component\form\field\Select;
use Eadmin\component\WatchComponent;
use Eadmin\constant\Style;

class ProgressCard extends WatchComponent
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
        switch ($value) {
            case 7:
                $this->content($this->render(rand(1000, 5000), rand(1, 100)));
                break;
            case 28:
                $this->content($this->render(rand(1000, 5000), rand(1, 100)));
                break;
            case 30:
                $this->content($this->render(rand(1000, 5000), rand(1, 100)));
                break;
            case 365:
                $this->content($this->render(rand(1000, 5000), rand(1, 100)));
                break;
        }
        return $this;
    }

    public function render($num,$per)
    {
        return Card::create([
            Html::create([
                Html::create('进度')->style(['marginLeft' => '20px', 'color' => '#2c2c2c', 'fontSize' => '16px']),
                $this->component,
            ])->tag('div')->style(Style::FLEX_BETWEEN_CENTER + ['padding' => '10px 0']),
            Html::create([
                Html::create($num)->style(['marginLeft' => '20px', 'fontSize' => '30px']),
                Html::create('New Users')->style(['marginRight' => '20px']),
            ])->tag('div')->style(Style::FLEX_BETWEEN_CENTER),
            Process::create()->percentage($per)->textInside()->strokeWidth(15)->style(['margin'=>'18px'])
        ])->bodyStyle(['padding' => '0']);
    }
}