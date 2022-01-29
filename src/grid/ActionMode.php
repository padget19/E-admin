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
    protected $addText = null;
    protected $editText = null;
    protected $delText = null;
    protected $detailText = null;
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
    /**
     * 添加按钮文本
     * @param null $text
     * @return $this
     */
    public function addText($text = null){
        if(is_null($text)){
            return $this->addText ?? admin_trans('admin.add');
        }else{

            $this->addText = $text;
            return $this;
        }

    }
    /**
     * 删除按钮文本
     * @param null $text
     * @return $this
     */
    public function delText($text = null){
        if(is_null($text)){
            return $this->delText ?? admin_trans('admin.delete');
        }else{
            $this->delText = $text;
            return $this;
        }

    }
    /**
     * 详情按钮文本
     * @param null $text
     * @return $this
     */
    public function detailText($text = null){
        if(is_null($text)){
            return $this->detailText ?? admin_trans('admin.detail');
        }else{
            $this->detailText = $text;
            return $this;
        }

    }
    /**
     * 编辑按钮文本
     * @param null $text
     * @return $this
     */
    public function editText($text = null){
        if(is_null($text)){
            return $this->editText ?? admin_trans('admin.edit');
        }else{
            $this->editText = $text;
            return $this;
        }
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
