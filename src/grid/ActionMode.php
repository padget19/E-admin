<?php


namespace Eadmin\grid;


use Eadmin\component\basic\Dialog;
use Eadmin\component\basic\Drawer;
use Eadmin\component\basic\Html;
use Eadmin\component\basic\Router;

class ActionMode
{
    protected $component;
    protected $form;
    protected $detail;

    public function __construct()
    {
        $this->component = Html::create();
    }

    public function form($form = null)
    {
        if (!is_null($form)) {
            $this->form = $form;
        }
        return $this->form;
    }

    public function detail($detail = null)
    {
        if (!is_null($detail)) {
            $this->detail = $detail;
        }
        return $this->detail;
    }

    /**
     * @return Dialog
     */
    public function dialog()
    {
        $this->component = Dialog::create();
        return $this->component;
    }
    
    /**
     * @return Drawer
     */
    public function drawer()
    {
        $this->component = Drawer::create();
        return $this->component;
    }

    public function component()
    {
        return $this->component;
    }
}
