<?php


namespace Eadmin\grid\excel;


use app\common\facade\Token;
use Eadmin\Queue;
class ExcelQueue extends Queue
{

    /**
     * 执行任务
     * @param $data
     * @return bool
     */
    public function handle($data): bool
    {
        unset($data['eadmin_queue']);
        $data['eadmin_queue_export'] = true;
        request()->withGet($data);
        $class    = request()->get('eadmin_class');
        $action   = request()->get('eadmin_function');
        $instance = app($class);
        $reflect  = new \ReflectionMethod($instance, $action);
        $class = explode('\\',$class);
        $controller = end($class);
        request()->setController($controller);
        $actionName = $reflect->getName();
        request()->setAction($actionName);
        $call     = app()->invokeReflectMethod($instance, $reflect, $data);
        $call->exportData();
        return true;
    }
}
