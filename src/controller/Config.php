<?php


namespace Eadmin\controller;


use Eadmin\Admin;
use Eadmin\Controller;
use Eadmin\form\Form;
use Eadmin\grid\Grid;
use Eadmin\grid\SidebarGrid;
use Eadmin\model\SystemConfig;
use Eadmin\model\SystemConfigCate;

class Config extends Controller
{
    protected $type = [
        'text' => '文本输入框',
        'number' => '数字输入框',
        'select' => '下拉框',
        'radio' => '单选框',
        'checkbox' => '多选框',
        'switch' => '开关',
        'textarea' => '多行文本框',
        'editor' => '富文本',
        'file' => '上传文件',
        'image' => '上传图片',
        'slider' => '滑块',
        'datetime' => '日期时间',
        'date' => '日期',
        'time' => '时间',
    ];
    public function index()
    {
        $sidebarGrid = SidebarGrid::create(new SystemConfigCate(), $this->grid())->treePid()
            ->form($this->cateForm())
            ->field('cate_id');
        return $sidebarGrid;
    }

    public function grid()
    {
        $grid = new Grid(new SystemConfig());
        $cate_id = $this->request->get('cate_id');
        $grid->model()->where('cate_id', $cate_id);
        $grid->sortDrag();
        $grid->column('label', '配置名称');
        $grid->column('name', '字段变量');
        $grid->column('type', '类型')->using($this->type);
        $grid->column('status', '显示')->switch();
        $grid->setForm($this->form($cate_id))->dialog();
        return $grid;
    }

    public function form($cate_id = 0)
    {
        $form = new Form(new SystemConfig);
        $options = Admin::menu()->listOptions(SystemConfigCate::select()->toArray());
        $form->select('cate_id','分类')
            ->options(array_column($options, 'label', 'id'))
            ->required();
        $form->text('label', '配置名称')->required();
        $form->text('name', '配置字段')->required()->uniqueRule();
        $form->switch('required', '必填')->default(0)->value($form->getData('attribute.required'));
        $form->text('help', '说明');
        $form->select('type', '类型')->options($this->type)->required()->when(['text', 'number'], function (Form $form) {
            $form->text('value', '默认值');
        })->when('textarea', function (Form $form) {
            $form->textarea('value', '默认值');
        })->when(['image','file'],function (Form $form){
            $form->switch('multiple', '多选')->default(0)->value($form->getData('attribute.multiple'));
        })->when(['select'], function (Form $form) {
            $form->switch('multiple', '多选')->default(0)->value($form->getData('attribute.multiple'));
            $form->hasMany('options', '选项', function (Form $form) {
                $form->row(function (Form $form) {
                    $form->text('value', '选项值')->md(12)->required();
                    $form->text('label', '选项文本')->md(12)->required();
                });
            })->table();
        })->when(['radio', 'checkbox'], function (Form $form) {
            $many = $form->hasMany('options', '选项', function (Form $form) {
                $form->row(function (Form $form) {
                    $form->text('value', '选项值')->md(12)->required();
                    $form->text('label', '选项文本')->md(12)->required();
                });
            })->table();
            $options = $form->getData('attribute.options');
            if($options){
                $many->value($options);
            }
        });
        $form->saving(function ($post) {
            $post['attribute'] = [
                'multiple' => $post['multiple'],
                'options' => $post['options'],
                'required' => $post['required'],
            ];
            return $post;
        });
        return $form;
    }
    public function cateForm()
    {
        $form = new Form(new SystemConfigCate);
        $options = Admin::menu()->listOptions(SystemConfigCate::select()->toArray());
        $form->select('pid','分类')
            ->options([0=>'顶级分类']+array_column($options, 'label', 'id'))
            ->required();
        $form->text('name', '分类名称')->required();
        $form->switch('status', '显示')->default(1);
        $form->number('sort', '排序')->default(0);
        return $form;
    }
}
