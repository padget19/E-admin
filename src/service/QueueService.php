<?php


namespace Eadmin\service;


use Eadmin\model\SystemQueue;
use Eadmin\Queue;
use think\facade\Db;

class QueueService extends Queue
{
    public function __construct($id = 0)
    {
        $this->queueId = $id;
    }
    public function retry(){
        $queue = SystemQueue::find($this->queueId);
        $data = $queue['queue_data'];
        $this->progress('任务重试');
        $data['system_queue_id'] = $this->queueId;
        queue($queue['queue'], $data);
    }
    /**
     * 添加队列任务
     * @param string $title 标题
     * @param string $job 任务
     * @param array $data 数据
     * @param int $delay 延迟时间
     * @return int|string
     */
    public function queue($title, $job, array $data, $delay = 0)
    {
        $id = Db::name('system_queue')->insertGetId([
            'name' => $title,
            'queue' => $job,
            'queue_data' => json_encode($data),
            'plan_time' => date('Y-m-d H:i:s', time() + $delay),
        ]);
        $data['system_queue_id'] = $id;
        queue($job,$data,$delay);
        return $id;
    }
}
