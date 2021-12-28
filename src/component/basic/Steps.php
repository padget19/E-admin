<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

class Steps extends Component
{
    protected $name = 'ASteps';

    public function __construct($value=0)
    {
        parent::__construct();
        $this->bindAttValue('current',$value);
    }
    /**
     * 步骤
     * @param string $title 标题
     * @param string $description 描述
     * @param string $icon 图标
     * @return Step
     */
    public function step($title='',$description='',$icon=''){
        $step  = new Step();
        $step->content($title,'title');
        $step->content($description,'description');
        $step->content($icon,'icon');
        $this->content($step);
        return $step;
    }
    public static function create($value=0){
        return new static($value);
    }
}
