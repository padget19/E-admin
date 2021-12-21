<?php


namespace Eadmin;

use think\facade\Cache;
use think\facade\Db;
use think\queue\Job;

/**
 * 系统队列
 * Class Queue
 * @package Eadmin
 * @property Job $job
 */
abstract class Queue
{
    protected $job;
    protected $tableName = 'system_queue';
    protected $queueId = 0;
    protected $time;

    public function init($job)
    {
        $this->time = microtime(true);
        $this->job = $job;
        $data = json_decode($this->job->getRawBody(), true);
        $this->queueId = $data['data']['system_queue_id'];
        $this->progress('任务开始', 0, 2);
    }

    /**
     * 更新进度百分比
     * @param int $total 总和
     * @param int $count 当前记录总数
     * @param string|null $message 描述
     */
    public function percentage(int $total,int $count,string $message = null){
        $total = $total < 1 ? 1 : $total;
        $progress = sprintf("%.2f", $count / $total * 100);
        $this->progress($message,$progress);
    }

    /**
     * 更新进度
     * @param string|null $message 描述
     * @param null $progress 百分比
     * @param null $status 状态 3完成，4失败
     * @return array|mixed
     * @throws \think\db\exception\DbException
     */
    public function progress(string $message = null, $progress = null, $status = null)
    {
        if (is_numeric($status)) {
            $update['status'] = $status;
            if ($status == 2) {
                $update['exec_time'] = date('Y-m-d H:i:s');
            }
            if ($status == 4 || $status == 3) {

                $update['task_time'] = microtime(true) - $this->time;
            }
            Db::name($this->tableName)->where('id', $this->queueId)->update($update);
        }
        $cacheKey = 'queue_' . $this->queueId . '_progress';
        $data = Cache::get($cacheKey) ?: [];
        if (!isset($data['status'])) {
            $data['progress'] = 0;
        }
        if (!isset($data['status'])) {
            $data['status'] = 1;
        }
        if (is_numeric($progress)) {
            $data['progress'] = $progress;
        }
        if (is_numeric($status)) {
            $data['status'] = $status;
        }
        if (!is_null($message) || !is_null($progress) || !is_null($status)) {
            $data['history'][] = ['message' => $message, 'progress' => $data['progress'], 'datetime' => date('Y-m-d H:i:s')];
        }

        Cache::set($cacheKey, $data, 86400);
        return $data;
    }

    /**
     * 重新发布
     * @param int $delay 延迟时间
     */
    public function release($delay = 0)
    {
        $this->job->release($delay);
        $this->progress('任务重新发布', 0, 1);
    }

    //执行失败
    public function error($message)
    {
        $this->job->delete();
        $this->progress($message, 100, 4);
    }

    //执行完成
    public function success($message)
    {
        $this->job->delete();
        $this->progress($message, 100, 3);
    }

    public function fire(Job $job, $data)
    {
        $this->init($job);
        try {
            if ($this->handel($data)) {
                $this->success('<b style="color: green">任务完成</b>'.PHP_EOL.PHP_EOL);
            } else {
                $this->error('<b style="color: red">任务失败</b>');
            }
        } catch (\Throwable $exception) {
            $this->error('<b style="color: red">任务失败错误信息</b>：' . $exception->getMessage());
            $this->error('<b style="color: red">任务失败追踪错误</b>：' . $exception->getTraceAsString());
        }
    }
}
