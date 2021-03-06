<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-06-26
 * Time: 08:53
 */

namespace Eadmin\chart;


use Eadmin\component\Component;

abstract class EchartAbstract extends Component
{
    protected $name = 'EadminChart';
    protected $series = [];
    protected $legend = [];
    protected $options = [];
    protected $width;
    protected $height;
    protected $colors = [];
    protected $hideLegend = false;
    public function __construct($height, $width)
    {
        $this->width = $width;
        $this->height = $height;
        $this->colors = Color::GRADUAL;
    }

    abstract function series(string $name, array $data,array $options = []);

    public function getColors()
    {
        if (count($this->colors) == 0) {
            $this->colors = Color::GRADUAL;
        }
        $colors = array_shift($this->colors);
        return $colors;
    }

    /**
     * 设置标题
     * @param string $text
     */
    public function title($text)
    {
        $this->options['text']['title'] = $text;
        return $this;
    }

    public function setOptions($options)
    {
        $this->options = array_merge($this->options, $options);
    }

    public function getOptions()
    {
        if (!empty($this->series)) {
            $this->options['series'] = $this->series;
        }
        if (!empty($this->legend) && !$this->hideLegend) {
            $this->options['legend']['data'] = $this->legend;
        }
        return $this->options;
    }
    public function hideLegend(){
        $this->hideLegend = true;
    }
    public function jsonSerialize()
    {
        $this->getOptions();
        $this->attr('options', $this->options);
        $this->attr('width', $this->width);
        $this->attr('height', $this->height);
        return parent::jsonSerialize(); // TODO: Change the autogenerated stub
    }
}
