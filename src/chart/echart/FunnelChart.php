<?php

namespace Eadmin\chart\echart;


use Eadmin\chart\Color;
use Eadmin\chart\EchartAbstract;

/**
 * 漏斗图
 * Class FunnelChart
 * @package Eadmin\chart
 */
class FunnelChart extends EchartAbstract
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
                'formatter' => '{a} <br/>{b} : {c} ({d}%)'
            ],

            'legend' => [
                'data' => [],
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
        $names          = array_column($data, 'name');
        $this->legend   = array_merge($this->legend, $names);
        $length         = count($this->series);
        $start          = $length * 30 + 10;
        $end            = ($length + 1) * 20;
        $this->series[] = array_merge([
            'name'      => $name,
            'type'      => 'funnel',
            'minSize'   => '0%',
            'maxSize'   => '100%',
            'top'       => 60,
            'bottom'    => 60,
            'gap'       => 2,
            'itemStyle' => [
                'borderColor' => '#fff',
                'borderWidth' => 1,
            ],
            'labelLine' => [
                'length'    => 10,
                'lineStyle' => [
                    'width' => 1,
                    'type'  => 'solid'
                ]
            ],
            'emphasis'  => [
                'label' => [
                    'fontSize' => 20
                ]
            ],
            'color'=>Color::ECHART,
            'data'      => $data,
        ],$options);
        return $this;
    }

}
