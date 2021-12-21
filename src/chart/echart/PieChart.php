<?php

namespace Eadmin\chart\echart;


use Eadmin\chart\Color;
use Eadmin\chart\EchartAbstract;

/**
 * 饼图
 * Class PieChart
 * @package Eadmin\chart
 */
class PieChart extends EchartAbstract
{
    public function __construct($height = '350px', $width = '100%')
    {
        parent::__construct($height, $width);
        $this->options = [
            'title'   => [
                'text' => '',
            ],
            'tooltip' => [
                'trigger'   => 'item',
            ],
            'legend' => [
                'left'   => 'center',
                'bottom' => '0',
                'data'   => [],
            ],
            'series' => []
        ];
    }

    /**
     * 设置数据源
     * @param string $name
     * @param array $data
     */
    public function series(string $name, array $data,array $options = [])
    {
        $names        = array_column($data, 'name');
        $this->legend = array_merge($this->legend, $names);
        $length       = count($this->series);
        $start        = $length * 30 + 10;
        $end          = ($length + 1) * 20;
        $this->series[] = array_merge([
            'name'              => $name,
            'type'              => 'pie',
            'roseType'          => 'radius',
            'radius'            => [$start, $start + $end],
            'center'            => ['50%', '38%'],
            'animationEasing'   => 'cubicInOut',
            'animationDuration' => 2600,
            'symbolSize'        => 8,
            'data'              => $data,
            'color'=>Color::ECHART
        ],$options);
        return $this;
    }

    /**
     * 返回视图
     * @return string
     */
    public function render()
    {
        if (count($this->series) == 1) {
            $this->series[0]['radius'] = [0, 100];
        }
        return parent::render();
    }
}
