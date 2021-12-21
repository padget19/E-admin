<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-07-01
 * Time: 22:59
 */

namespace Eadmin\controller;


use Eadmin\Admin;
use Eadmin\component\basic\Button;
use Eadmin\component\form\FormAction;
use Eadmin\component\layout\Content;
use Eadmin\component\layout\Row;
use Eadmin\Controller;
use Eadmin\form\drive\Config;
use Eadmin\grid\Actions;
use Eadmin\grid\Grid;
use Eadmin\service\BackupData;
use Eadmin\form\Form;

/**
 * 数据库备份
 * Class Backup
 * @package
 */
class Backup extends Controller
{
    /**
     * 数据库备份列表
     * @auth true
     * @login true
     * @return $this
     */
    public function index(): Grid
    {
        $data = BackupData::instance()->getBackUpList();
        return Grid::create($data,function (Grid $grid){
            $grid->title('数据库备份');
            $grid->column('name', '备份名称');
            $grid->column('size', '备份大小');
            $grid->column('create_time', '备份时间');
            $grid->actions(function (Actions $actions, $data) {
                $actions->prepend(
                    Button::create('还原')
                        ->typePrimary()
                        ->sizeSmall()
                        ->save(['id' => $data['id']], 'backup/reduction', '确认还原备份？')
                );
            });
            $grid->deling(function ($ids) {
                foreach ($ids as $id) {
                    BackupData::instance()->delete($id);
                }
            });
            $grid->hideDeleteButton();
            $grid->tools('backup/config');
        });
    }


    /**
     * 备份配置
     * @auth true
     * @login true
     */
    public function config()
    {
        return Form::create(new Config(),function (Form $form){
            $form->inline();
            $form->size('mini');
            $form->radio('databackup_on', '自动备份')->options([
                1 => '开启',
                0 => '关闭',
            ])->default(0)->themeButton();
            $form->number('database_number', '最多保留')->min(1)->append('<span style="padding-left: 12px">份</span>')->required();
            $form->number('database_day', '	数据库每')->min(1)->append('<span style="padding-left: 12px">天自动备份</span>')->required();
            $form->actions(function (FormAction $action) {
                $action->submitButton()->sizeMini();
                $action->addRightAction(Button::create('备份数据库')->typeWarning()->sizeMini()->save([], 'backup/add'));
                $action->hideResetButton();
            });
        });
    }

    /**
     * 还原数据库
     * @auth true
     * @login true
     */
    public function reduction($id)
    {
        if (BackupData::instance()->reduction($id)) {
            admin_success_message('数据库还原完成');
        } else {
            admin_error_message('数据库还原失败');
        }
    }

    /**
     * 备份数据库
     * @auth true
     * @login true
     */
    public function add()
    {
        $res = BackupData::instance()->backup();
        if ($res === true) {
            
            admin_success_message('数据库备份成功')->refresh();
        } else {
            admin_error_message($res);
        }
    }
}
