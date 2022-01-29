<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-06-25
 * Time: 23:56
 */

namespace Eadmin\component\form\step;


use Eadmin\component\basic\Button;
use Eadmin\component\basic\Html;
use Eadmin\component\basic\Result;
use Eadmin\component\basic\Steps;
use Eadmin\component\Component;

class FormSteps extends Steps
{
    protected $form;
    protected $result;
    protected $finish;

    public function __construct($form)
    {
        parent::__construct();
        $this->form = $form;
        $this->width();
    }

    /**
     * 设置宽度
     * @param $width
     * @return $this
     */
    public function width($width = 1000)
    {
        $this->form->alignCenter($width);
        return $this;
    }

    /**
     * 步骤表单
     * @param \Closure $closure
     * @param string $title 标题
     * @param string $description 描述
     * @param string $icon 图标
     * @return $this
     */
    public function add(\Closure $closure, $title, $description = '', $icon = '')
    {
        $formItems = $this->form->collectFields($closure);
        $this->step($title, $description, $icon);
        $html = Html::div()->attr('setpItem',true)->style(['margin:40px 80px 0px 80px']);
        $active = $this->bindAttr('current');
        foreach ($formItems as $item) {
            $count = count($this->content['default']) - 1;
            $html->content($item)->where($active, $count);
        }
        $this->form->push($html);
        return $this;
    }

    /**
     * 返回步骤数量
     * @return int
     */
    public function count(){
        if(isset($this->content['default'])){
            return count($this->content['default']);
        }
        return 0;
    }
    /**
     * 完成结果步骤
     * @param \Closure $closure
     * @param string $title 完成步骤标题
     */
    public function finish(\Closure $closure, $title = null)
    {
        
        $this->step($title ?? admin_trans('admin.complete'));
        $this->finish = $closure;
    }

    public function getClosure()
    {
        return $this->finish;
    }
}
