<?php


namespace Eadmin\controller;


use Eadmin\component\basic\Button;
use Eadmin\component\basic\Html;
use Eadmin\component\basic\TimeLine;
use Eadmin\component\form\FormAction;
use Eadmin\Controller;
use Eadmin\facade\Schedule;
use Eadmin\form\Form;
use Eadmin\grid\Actions;
use Eadmin\grid\Grid;
use think\facade\Cache;

class Crontab extends Controller
{
    public function index()
    {
        $list = Schedule::list();
        $grid = new Grid($list);
        $grid->column('name', '任务名称');
        $grid->column('schedule', '执行时机')->display(function ($val, $data) {
            return $val->getDesc();
        });
        $grid->column('file', '注册路径');
        $grid->hideTools();
        $grid->actions(function (Actions $actions, $data) {
            $actions->hideDel();
            $actions->append([
                Button::create('日志')
                    ->icon('el-icon-document')
                    ->dialog()->width('70%')
                    ->form($this->log($data['id'])),
                Button::create('执行')->typePrimary()->save(['id' => $data['id']], 'crontab/exec'),
            ]);
        });
        $grid->hideSelection();
        return $grid;
    }
    //查看日志
    public function log($id){
        $list = Schedule::list();
        $logs = $list[$id]['log'];
        $timeline = TimeLine::create();
        foreach ($logs as $log) {
            $timeline->item($log['message'])->timestamp($log['time']);
        }
        $form = new Form([]);
        $form->push($timeline->style(['maxHeight'=>'400px','overflow'=>'auto']));
        $form->actions(function (FormAction $actions) use($id){
            $actions->hideResetButton();
            $actions->hideSubmitButton();
            $actions->cancelButton();
            $actions->addLeftAction(
                Button::create('清空日志')
                    ->typeInfo()
                    ->save(['id' => $id], 'crontab/clear')
            );
        });
        return $form;
    }
    //清除日志
    public function clear($id)
    {
        $cacheKey = 'crontab_' . $id . '_log';
        Cache::delete($cacheKey);
        admin_success_message('任务已执行!');
    }
    //执行任务
    public function exec($id)
    {
        $list = Schedule::list();
        $list[$id]['schedule']->run(true);
        admin_success_message('任务已执行!');
    }
}
