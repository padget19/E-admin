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
use Eadmin\model\SystemAuth;
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
            $grid->title('系统角色管理');
            $grid->column('name', '角色名称');
            $grid->column('desc', '角色描述');
            $grid->column('status', '状态')->switch();
            $grid->actions(function (Actions $action, $data) {
                $action->hideDetail();
                $button = Button::create('权限授权')
                    ->type('primary')
                    ->plain()
                    ->size('small')
                    ->icon('el-icon-s-check')
                    ->dialog()
                    ->width('70%')
                    ->title('权限授权')
                    ->form($this->authNode($data['id']));
                $action->prepend($button);
                $button = Button::create('菜单授权')
                    ->type('primary')
                    ->plain()
                    ->size('small')
                    ->icon('el-icon-menu')
                    ->dialog()
                    ->title('菜单授权')
                    ->form($this->menu($data['id']));
                $action->prepend($button);
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
            $form->text('name', '权限名称')->required();
            $form->textarea('desc', '权限描述')->rows(4)->required();
        });
    }

    /**
     * 菜单授权
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
                ->data([['name' => '全选', 'id' => 0, 'children' => Admin::menu()->tree()]])
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
     * 权限授权
     * @auth true
     * @login true
     * @return string
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
