<?php

namespace Eadmin\controller;

use Eadmin\component\basic\Button;
use Eadmin\component\basic\Html;
use Eadmin\component\basic\Tag;
use Eadmin\Controller;
use Eadmin\form\Form;
use Eadmin\grid\Actions;
use Eadmin\detail\Detail;
use Eadmin\grid\Filter;
use Eadmin\grid\Grid;
use Eadmin\model\SystemQueue;
use Eadmin\service\QueueService;

/**
 * 队列任务
 * Class Queue
 * @package app\admin\controller
 */
class Queue extends Controller
{
    protected $title = '队列任务';
    protected $status = [1 => '等待处理', 2 => '正在执行', 3 => '已完成', 4 => '已失败'];

    /**
     * 列表
     * @auth false
     * @login true
     * @return Grid
     */
    public function index(): Grid
    {
        return Grid::create(new SystemQueue(), function (Grid $grid) {
            $grid->title($this->title);
            $grid->column('name', '任务信息')->display(function ($val, $data) {
                return Html::create([
                    Html::create('任务名称: ' . $data['name'])->tag('p'),
                    Html::create('队列名称: ' . $data['queue'])->tag('p'),
                ]);
            });

            $grid->column('exec_time', '执行时间')->display(function ($val, $data) {
                $task_time = round($data['task_time'], 4);
                return Html::create([
                    Html::create('执行时间: ' . $data['exec_time'])->when($task_time, function (Html $html) use ($task_time) {
                        $html->content(" 耗时 <span style='color: #2d8cf0'>{$task_time}</span> 秒");
                    })->tag('p'),
                    Html::create('计划时间: ' . $data['plan_time'])->tag('p'),
                    Html::create('创建时间: ' . $data['create_time'])->tag('p'),
                ]);
            });
            $grid->column('status', '状态')->using($this->status, [1 => 'info', 2 => '', 3 => 'success', 4 => 'danger']);
            $grid->filter(function (Filter $filter) {
                $filter->like('name', '名称');
                $filter->eq('status', '状态')->select($this->status);
                $filter->dateRange('create_time', '计划时间');
                $filter->dateRange('create_time', '执行时间');
                $filter->dateRange('create_time', '创建时间');
            });
            $grid->quickSearch();
            // 操作工具栏
            $grid->actions(function (Actions $action, $data) {

                $action->prepend([
                    Button::create('日志')
                        ->sizeSmall()
                        ->icon('el-icon-document')
                        ->dialog()
                        ->content(\Eadmin\Admin::view(__DIR__.'/../view/queue.vue')
                            ->attrs([
                                'id'=>$data['id'],
                                'title'=>$data['name'],
                                'queue'=>json_encode($data['queue_data'],JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
                            ])),
                    Button::create('重试')
                        ->sizeSmall()
                        ->typePrimary()
                        ->icon('fa fa-dot-circle-o')
                        ->save(['system_queue_id'=>$data['id']],'/queue/retry')
                        ->whenShow($data['status'] == 4)
                ]);
            });
        });
    }

    /**
     * 重试
     * @auth false
     * @login true
     */
    public function retry($system_queue_id){
        $queue = new QueueService($system_queue_id);
        $queue->retry();
        admin_success_message('操作成功');
    }
}
