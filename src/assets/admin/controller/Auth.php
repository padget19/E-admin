<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-25
 * Time: 16:38
 */

namespace app\admin\controller;

use Eadmin\component\basic\Button;
use Eadmin\component\form\FormAction;
use Eadmin\Controller;
use Eadmin\form\Form;
use Eadmin\grid\Actions;
use Eadmin\grid\Grid;
use Eadmin\model\AdminModel;
use Eadmin\model\SystemAuth;
use Eadmin\model\SystemAuthData;
use Eadmin\model\SystemAuthMenu;
use Eadmin\model\SystemAuthNode;
use Eadmin\Admin;

/**
 * 系统角色管理
 * Class Auth
 * @package app\admin\controller
 */
class Auth extends Controller
{
    /**
     * 系统角色列表
     * @auth true
     * @login true
     * @return Grid
     */
    public function index(): Grid
    {
        return Grid::create(new SystemAuth(), function (Grid $grid) {
            $grid->title(admin_trans('auth.title'));
            $grid->treeTable();
            $grid->column('name', admin_trans('auth.fields.name'));
            $grid->column('desc', admin_trans('auth.fields.desc'));
            $grid->column('status', admin_trans('auth.fields.status'))->switch();
            $grid->actions(function (Actions $action, $data) {
                $dropdown = $action->dropdown();
                $dropdown->prepend(admin_trans('auth.auth_grant'),'el-icon-s-check')
                    ->dialog()
                    ->width('70%')
                    ->title(admin_trans('auth.auth_grant'))
                    ->form($this->authNode($data['id']));
                $dropdown->prepend(admin_trans('auth.data_grant'),'fa fa-database')
                    ->dialog()
                    ->width('50%')
                    ->title(admin_trans('auth.data_grant'))
                    ->form($this->dataAuth($data['id'],1));
                $dropdown->prepend(admin_trans('auth.menu_grant'),'el-icon-menu')->dialog()
                    ->title(admin_trans('auth.menu_grant'))
                    ->form($this->menu($data['id']));
            });
            $grid->setForm($this->form())->dialog();
        });
    }

    /**
     * 系统角色
     * @auth true
     * @login true
     * @return Form
     */
    public function form(): Form
    {
        return Form::create(new SystemAuth(), function (Form $form) {
            $options = SystemAuth::field('id,name,pid')->select()->toArray();
            $form->select('pid',admin_trans('auth.parent'))
                ->treeOptions($options);
            $form->text('name', admin_trans('auth.fields.name'))->required();
            $form->textarea('desc', admin_trans('auth.fields.desc'))->rows(4)->required();
        });
    }
    /**
     * 数据权限
     * @auth true
     * @login true
     * @return Form
     */
    public function dataAuth($id,$type){
        return Form::create([], function (Form $form) use ($id,$type) {
            $data = SystemAuthData::where('auth_type',$type)
                ->where('auth_id',$id)
                ->where('data_type',1)
                ->column('data_id');
            $form->selectTable('group_data',admin_trans('auth.select_group'))
                ->from($this->index())
                ->tip(admin_trans('auth.select_group_tip'))
                ->multiple()
                ->value($data)
                ->options(function ($ids){
                    return SystemAuth::whereIn('id',$ids)->column('name','id');
                });
            $data = SystemAuthData::where('auth_type',$type)
                ->where('auth_id',$id)
                ->where('data_type',2)
                ->column('data_id');
            $form->selectTable('user_data',admin_trans('auth.select_user'))
                ->from(url('admin/index'))
                ->value($data)
                ->multiple()
                ->options(function ($ids){
                    return AdminModel::whereIn('id',$ids)->column('nickname','id');
                })->tip(admin_trans('auth.select_user_tip'));
            $form->saving(function ($data) use($type) {
                SystemAuthData::where('auth_type',$type)->where('auth_id', $data['id'])->delete();
                $insertData = [];
                foreach ($data['group_data'] as $data_id) {
                    $insertData[] = [
                        'auth_type'=>$type,
                        'auth_id' => $data['id'],
                        'data_type' => 1,
                        'data_id' => $data_id,
                    ];
                }
                foreach ($data['user_data'] as $data_id) {
                    $insertData[] = [
                        'auth_type'=>$type,
                        'auth_id' => $data['id'],
                        'data_type' => 2,
                        'data_id' => $data_id,
                    ];
                }
                (new SystemAuthData())->saveAll($insertData);
            });
        });
    }
    /**
     * 菜单权限
     * @auth true
     * @login true
     * @return Form
     */
    public function menu($id)
    {
        return Form::create(new SystemAuth(), function (Form $form) use ($id) {
            $form->edit($id);
            $form->labelPosition('top');
            $menus = SystemAuthMenu::where('auth_id', request()->get('id'))->column('menu_id');
            $form->tree('menu_nodes')
                ->data([['name' => admin_trans('auth.all'), 'id' => 0, 'children' => Admin::menu()->tree()]])
                ->showCheckbox()
                ->value($menus)
                ->props(['children' => 'children', 'label' => 'name'])
                ->defaultExpandAll();
            $form->saving(function ($data) {
                SystemAuthMenu::where('auth_id', $data['id'])->delete();
                if (!empty($data['menu_nodes']) && count($data['menu_nodes']) > 0) {
                    $menuData = [];
                    foreach ($data['menu_nodes'] as $menuId) {
                        $menuData[] = [
                            'auth_id' => $data['id'],
                            'menu_id' => $menuId,
                        ];
                    }
                    (new SystemAuthMenu())->saveAll($menuData);
                }
            });
        });
    }

    /**
     * 功能权限
     * @auth true
     * @login true
     */
    public function authNode($id)
    {
        return Form::create(new SystemAuth(), function (Form $form) use ($id) {
            $form->edit($id);
            $form->labelPosition('top');
            $nodes = SystemAuthNode::where('auth_id', $id)->column('node_id');
            $form->tree('auth_nodes')
                ->data(Admin::node()->tree())
                ->showCheckbox()
                ->horizontal()
                ->value($nodes)
                ->defaultExpandAll();
            $form->saving(function ($data) {
                SystemAuthNode::where('auth_id', $data['id'])->delete();
                if (!empty($data['auth_nodes']) && count($data['auth_nodes']) > 0) {
                    $authData = [];
                    $nodes = Admin::node()->all();
                    foreach ($nodes as $node) {
                        if (in_array($node['id'], $data['auth_nodes'])) {
                            $authData[] = [
                                'auth_id' => $data['id'],
                                'node_id' => $node['id'],
                                'method' => $node['method'],
                                'class' => $node['class'],
                                'action' => $node['action'],
                            ];
                        }
                    }
                    if (count($authData) > 0) {
                        (new SystemAuthNode())->saveAll($authData);
                    }
                }
            });
        });
    }
}
