<?php


namespace Eadmin\grid\excel;


use Eadmin\Queue;
class ExcelQueue extends Queue
{

    /**
     * 执行任务
     * @param $data
     * @return bool
     */
    public function handel($data): bool
    {
        unset($data['eadmin_queue']);
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
