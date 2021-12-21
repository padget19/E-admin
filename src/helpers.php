<?php

use rockySysLog\model\SystemLog;
use admin\service\TokenService;
use Eadmin\Admin;
if (!function_exists('redis')) {
    /**
     * @return \Redis
     */
    function redis()
    {
        return \think\facade\Cache::store('redis');
    }
}
if (!function_exists('sysqueue')) {
    /**
     * @param string $title 标题
     * @param string $job 任务
     * @param array $data 数据
     * @param int $delay 延迟时间
     * @return mixed
     */
    function sysqueue($title,$job, array $data,$delay = 0)
    {
        return Admin::queue($title,$job,$data,$delay);
    }
}
if (!function_exists('sysconf')) {
    function sysconf($name, $value = null)
    {
        return Admin::sysconf($name, $value);
    }
}
if (!function_exists('admin_log')) {
    function admin_log($action, $content)
    {
        SystemLog::create([
            'username' => TokenService::instance()->user()->nickname ?? 'cli',
            'geoip'    => request()->ip(),
            'action'   => $action,
            'node'     => request()->url(),
            'content'  => $content,
        ]);
    }
}

if (!function_exists('admin_success')) {
    /**
     * @param $title
     * @param $message
     * @return \Eadmin\component\basic\Notification
     */
    function admin_success($title, $message)
    {
        return Admin::notification()->success($title, $message);
    }
}

if (!function_exists('admin_error')) {
    /**
     * @param $title
     * @param $message
     * @return \Eadmin\component\basic\Notification
     */
    function admin_error($title, $message)
    {
        return Admin::notification()->error($title, $message);
    }
}
if (!function_exists('admin_info')) {
    /**
     * @param $title
     * @param $message
     * @return \Eadmin\component\basic\Notification
     */
    function admin_info($title, $message)
    {
        return Admin::notification()->info($title, $message);
    }
}
if (!function_exists('admin_warn')) {
    /**
     * @param $title
     * @param $message
     * @return \Eadmin\component\basic\Notification
     */
    function admin_warn($title, $message)
    {
        return Admin::notification()->warning($title, $message);
    }
}

if (!function_exists('admin_warn_message')) {
    /**
     * @param $message
     * @return \Eadmin\component\basic\Message
     */
    function admin_warn_message($message)
    {
        return Admin::message()->warning($message);
    }
}
if (!function_exists('admin_success_message')) {
    /**
     * @param $message
     * @return \Eadmin\component\basic\Message
     */
    function admin_success_message($message)
    {
        return Admin::message()->success($message);
    }
}
if (!function_exists('admin_error_message')) {
    /**
     * @param $message
     * @return \Eadmin\component\basic\Message
     */
    function admin_error_message($message)
    {
        return Admin::message()->error($message);
    }
}
if (!function_exists('admin_info_message')) {
    /**
     * @param $message
     * @return \Eadmin\component\basic\Message
     */
    function admin_info_message($message)
    {
        return Admin::message()->info($message);
    }
}

