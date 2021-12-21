<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-28
 * Time: 21:38
 */

namespace app\admin\controller;


use Eadmin\component\basic\Badge;
use Eadmin\component\basic\Button;
use Eadmin\component\basic\Dialog;
use Eadmin\component\basic\DropdownItem;
use Eadmin\component\basic\Html;
use Eadmin\component\basic\Tag;
use Eadmin\component\grid\BatchAction;
use Eadmin\component\layout\Content;
use Eadmin\Controller;
use Eadmin\form\Form;
use Eadmin\grid\Actions;
use Eadmin\grid\Filter;
use Eadmin\grid\Grid;
use Eadmin\model\AdminModel;
use Eadmin\model\SystemAuth;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;


/**
 * 系统用户管理
 * Class Admin
 * @package app\admin\controller
 */
class Admin extends Controller
{
    protected $title = '系统用户';

    /**
     * 系统用户列表
     * @auth true
     * @login true
     * @method get
     */
    public function index(): Grid
    {
        return Grid::create(new AdminModel(), function (Grid $grid) {
            $grid->title($this->title);
            $grid->userInfo('avatar', 'nickname', '头像');
            $grid->column('username', '用户账号')->display(function ($val, $data) {
                if ($data['id'] == config('admin.admin_auth_id')) {
                    return Html::create()
                        ->content($val)
                        ->content(
                            Badge::create()->value('超级管理员')->type('primary')->attr('style', ['marginTop' => '5px'])
                        );
                } else {
                    return $val;
                }
            });
            $grid->column('phone', '手机号');
            $grid->column('mail', '邮箱');
            $grid->column('status', '账号状态')->switch();
            $grid->column('create_time', '创建时间');

            $grid->actions(function (Actions $action, $data) {
                if ($data['id'] == config('admin.admin_auth_id') || $data['id'] == \Eadmin\Admin::id()) {
                    $action->hideDel();
                }

                $action->hideDetail();
                $button = Button::create('重置密码')
                    ->type('primary')
                    ->size('small')
                    ->icon('el-icon-key')
                    ->plain()
                    ->dialog()
                    ->title('重置密码')
                    ->form($this->resetPassword($data['id']));
                $action->prepend($button);

            });
            //删掉前回调
            $grid->deling(function ($ids) {
                if (is_array($ids) && in_array(config('admin.admin_auth_id'), $ids)) {
                    $this->errorCode(999, '超级管理员不可以删除噢!');
                }
            });
            //更新前回调
            $grid->updateing(function ($ids, $data) {
                if (in_array(config('admin.admin_auth_id'), $ids)) {
                    if (isset($data['status']) && $data['status'] == 0) {
                        $this->errorCode(999, '超级管理员不可以禁用噢!');
                    }
                }
            });
            $grid->filter(function (Filter $filter) {
                $filter->like('username', '用户账号');
                $filter->like('phone', '手机号');
                $filter->eq('status', '账号状态')->select([
                    1 => '正常',
                    0 => '已禁用'
                ]);
                $filter->dateRange('create_time', '创建时间');
            });
            $grid->quickSearch();
            $grid->setForm($this->form())->dialog();
			$grid->hideDeleteButton();
        });

    }

    /**
     * 修改密码
     * @auth true
     * @login true
     */
    public function updatePassword()
    {
        return Form::create(new AdminModel(),function (Form $form){
            $form->edit(\Eadmin\Admin::id());
            $form->password('old_password', '旧密码')->required();
            $form->password('new_password', '新密码')->rule([
                'confirm' => '输入密码不一致',
                'min:5' => '密码最少5位数'
            ])->required();
            $form->password('new_password_confirm', '确认密码')->required();
            $form->saving(function ($data) use ($form) {
                if (!password_verify($data['old_password'], $form->getData('password'))) {
                    admin_error_message('旧密码错误');
                }
                $data['password'] = $data['new_password'];
                return $data;
            });
        });

    }

    /**
     * 重置密码
     * @auth true
     * @login true
	 * @param int $id 系统用户id
     */
    public function resetPassword($id)
    {
        return Form::create(new AdminModel(),function (Form $form) use ($id){
            $form->edit($id);
            $form->password('new_password', '新密码')->required()->rule([
                'confirm' => '输入密码不一致',
                'min:5' => '密码最少5位数'
            ]);
            $form->password('new_password_confirm', '确认密码')->required();
            $form->saving(function ($data) {
                $data['password'] = $data['new_password'];
                return $data;
            });
        });
    }

    /**
     * 系统用户
     * @auth true
     * @login true
     */
    public function form(): Form
    {
        return Form::create(new AdminModel(),function (Form $form){
            $userInput = $form->text('username', '用户账号')->rule([
                'chsDash' => '用户账号只能是汉字、字母、数字和下划线_及破折号-',
                'unique:system_user' => '用户名存在重复'
            ])->required();
            if ($form->isEdit()) {
                $userInput->disabled();
            }
            $form->text('nickname', '用户昵称')->rule([
                'chsAlphaNum' => '用户昵称只能是汉字、字母和数字',
            ])->required();
            $form->image('avatar', '用户头像')->required();
            if (!$form->isEdit()) {
                $form->password('password', '密码')->rule(['min:5' => '密码最少5位数'])->default(123456)->help('初始化密码123456,建议密码包含大小写字母、数字、符号')->required();
            }
            $form->mobile('phone', '手机号')
                ->rule([
                    'unique:' . config('admin.system_user_table') => '手机号已存在'
                ]);
            $form->text('mail', '邮箱')->rule([
                'email' => '请输入正确的邮箱',
            ]);
            if ($form->getData('id') != config('admin.admin_auth_id')) {
                $auths = SystemAuth::where('status',1)->column('name', 'id');
                $form->checkbox('roles', '访问权限')->options($auths, true);
            }
        });
    }

    /**
     * 获取个人信息
     * @auth false
     * @login true
     */
    public function info()
    {
        $data['menus'] = \Eadmin\Admin::menu()->tree(true);
        $data['info'] = \Eadmin\Admin::user();
        $data['webLogo'] = sysconf('web_logo');
        $data['webName'] = sysconf('web_name');
        $data['dropdownMenu'] = [
            DropdownItem::create(Dialog::create('个人信息')->title('个人信息')->form($this->editInfo())->appendToBody(true)),
            DropdownItem::create(Dialog::create('修改密码')->title('修改密码')->form($this->updatePassword())->appendToBody(true)),
        ];
        $this->successCode($data);
    }

    /**
     * 编辑个人信息
     * @auth false
     * @login true
     */
    public function editInfo()
    {
        return Form::create(new AdminModel(),function (Form $form){
            $form->edit(\Eadmin\Admin::id());
            $form->text('username', '用户名')->rule([
                'chsDash' => '用户名只能是汉字、字母、数字和下划线_及破折号-'
            ])->disabled();
            $form->text('nickname', '用户昵称')->rule([
                'chsAlphaNum' => '用户昵称只能是汉字、字母和数字',
            ])->required();
            $form->image('avatar', '用户头像')->default($this->request->domain() . '/static/img/headimg.png')->size(80, 80);
            $form->text('phone', '手机号')
                ->rule([
                    'mobile' => '请输入正确的手机号',
                    'unique:' . config('admin.system_user_table') => '手机号已存在'
                ]);
            $form->text('mail', '邮箱')->rule([
                'email' => '请输入正确的邮箱',
            ]);
        });
    }

    /**
     * 刷新token
     * @auth false
     * @login true
     */
    public function refreshToken()
    {
        $tokens = \Eadmin\Admin::token()->refresh();
        $this->successCode($tokens);
    }


}
