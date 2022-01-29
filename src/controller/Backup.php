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
            $grid->title(admin_trans('backup.backup_database'));
            $grid->column('name', admin_trans('backup.fields.name'));
            $grid->column('size', admin_trans('backup.fields.size'));
            $grid->column('create_time', admin_trans('backup.fields.time'));
            $grid->actions(function (Actions $actions, $data) {
                $actions->prepend(
                    Button::create(admin_trans('backup.reduction'))
                        ->typePrimary()
                        ->sizeSmall()
                        ->save(['id' => $data['id']], 'backup/reduction', admin_trans('backup.confirm_reduction'))
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
            $form->radio('databackup_on', admin_trans('backup.auto'))->options(admin_trans('backup.options.databackup_on'))->default(0)->themeButton();
            $form->number('database_number', admin_trans('backup.max_retention'))->min(1)->append('<span style="padding-left: 12px">'.admin_trans('backup.fen').'</span>')->required();
            $form->number('database_day', ' '.admin_trans('backup.every_database'))->min(1)->append('<span style="padding-left: 12px">'.admin_trans('backup.day_auto').'</span>')->required();
            $form->actions(function (FormAction $action) {
                $action->submitButton()->sizeMini();
                $action->addRightAction(Button::create(admin_trans('backup.backup_database'))->typeWarning()->sizeMini()->save([], 'backup/add'));
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
            admin_success_message(admin_trans('backup.reduction'));
        } else {
            admin_error_message(admin_trans('backup.fail'));
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

            admin_success_message(admin_trans('backup.success'))->refresh();
        } else {
            admin_error_message($res);
        }
    }
}
