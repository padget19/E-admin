<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-13
 * Time: 20:28
 */

namespace Eadmin\controller;


use Eadmin\Admin;
use Eadmin\component\basic\Button;
use Eadmin\component\basic\Dropdown;
use Eadmin\component\basic\DropdownItem;
use Eadmin\component\basic\Html;
use Eadmin\component\basic\Tip;
use Eadmin\component\layout\Content;
use Eadmin\component\layout\Row;
use Eadmin\Controller;
use Eadmin\grid\Actions;
use Eadmin\form\Form;
use Eadmin\detail\Detail;
use Eadmin\grid\Filter;
use Eadmin\grid\Grid;
use Eadmin\model\SystemAuthMenu;
use Eadmin\model\SystemMenu;
use Eadmin\service\MenuService;
use think\facade\Filesystem;


/**
 * 系统菜单管理
 * Class Menu
 * @package app\admin\controller
 */
class Menu extends Controller
{
    /**
     * 系统菜单管理
     * @auth true
     * @login true
     * @return Grid
     */
    public function index()
    {
        return Grid::create(new SystemMenu(),function (Grid $grid){
            $grid->treeTable();
            $grid->title('系统菜单管理');
            $grid->column('name', '菜单名称')->display(function ($val, $data) {
                return "<i class='{$data['icon']}'></i> " . $val;
            });
            $grid->column('url', '菜单链接')->display(function ($val) {
                return ' ' . $val;
            });
            $grid->column('status', '状态')->switch();
            $grid->column('admin_visible', '超级管理员状态')->switch([[1 => '显示'], [0 => '隐藏']]);
            $grid->actions(function (Actions $action, $data) {
                $action->prepend(
                    Button::create('添加菜单')
                        ->plain()
                        ->sizeSmall()
                        ->typePrimary()
                        ->dialog()
                        ->form($this->form($data['id']))
                );
                $action->hideDetail();

            });
            $grid->sortInput();
            $grid->setForm($this->form())->dialog();
            $grid->quickSearch();
        });
    }

    /**
     * 系统菜单
     * @auth true
     * @login true
     * @return Form
     */
    public function form($pid=0): Form
    {
        return Form::create(new SystemMenu(),function (Form $form) use($pid){
            $menus = Admin::menu()->listOptions();
            $form->select('pid', '上级菜单')->default($pid)
                ->options([0 => '顶级菜单'] + array_column($menus, 'label', 'id'))
                ->required();
            $form->text('name', '菜单名称')->required();
            $form->text('url', '菜单链接');
            $form->icon('icon', '菜单图标');
            $form->number('sort', '排序')->default(SystemMenu::where('pid',$pid)->max('sort')+1);
        });
    }
}
