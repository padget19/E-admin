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


if (!function_exists('hex2rgba')) {
    /**
     * 十六进制转化为RGB
     * @param string $hexColor 颜色
     * @return array
     */
    function hex2rgba($hexColor)
    {
        $color = str_replace('#', '', $hexColor);
        if (strlen($color) > 3) {
            $rgb = array(
                'r' => hexdec(substr($color, 0, 2)),
                'g' => hexdec(substr($color, 2, 2)),
                'b' => hexdec(substr($color, 4, 2))
            );
        } else {
            $color = str_replace('#', '', $hexColor);
            $r = substr($color, 0, 1) . substr($color, 0, 1);
            $g = substr($color, 1, 1) . substr($color, 1, 1);
            $b = substr($color, 2, 1) . substr($color, 2, 1);
            $rgb = array(
                'r' => hexdec($r),
                'g' => hexdec($g),
                'b' => hexdec($b)
            );
        }
        return $rgb;
    }
}
if (!function_exists('admin_trans')) {
    /**
     * 获取语言变量值
     * @param string $name 语言变量名
     * @param array  $vars 动态变量值
     * @param string $lang 语言
     * @return mixed
     */
    function admin_trans(string $name, array $vars = [], string $lang = ''){
        return app('admin.translator')->trans($name,$vars,$lang);
    }
}
if (!function_exists('rgbToHex')) {
    /**
     * RGB转 十六进制
     * @param string $rgb 颜色
     * @return array
     */
    function rgbToHex($rgb)
    {
        $regexp = "/^rgb\(([0-9]{0,3})\,\s*([0-9]{0,3})\,\s*([0-9]{0,3})\)/";
        $re = preg_match($regexp, $rgb, $match);
        $re = array_shift($match);
        $hexColor = "#";
        $hex = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
        for ($i = 0; $i < 3; $i++) {
            $r = null;
            $c = $match[$i];
            $hexAr = array();
            while ($c > 16) {
                $r = $c % 16;
                $c = ($c / 16) >> 0;
                array_push($hexAr, $hex[$r]);
            }
            array_push($hexAr, $hex[$c]);
            $ret = array_reverse($hexAr);
            $item = implode('', $ret);
            $item = str_pad($item, 2, '0', STR_PAD_LEFT);
            $hexColor .= $item;
        }
        return $hexColor;
    }
}
if (!function_exists('color_mix')) {
    /**
     * 混合颜色
     * @param $color1 十六进制颜色1
     * @param $color2 十六进制颜色2
     * @param int $rate 百分比
     * @return array
     */
    function color_mix($color1,$color2,$rate=50)
    {
        $rgb1 = hex2rgba($color1);
        $rgb2 = hex2rgba($color2);
        //颜色A-(颜色A-颜色B)*(1-颜色A的百分比)
        $r = round($rgb1['r']-($rgb1['r']-$rgb2['r']) * (1-$rate*0.01));
        $g = round($rgb1['g']-($rgb1['g']-$rgb2['g']) * (1-$rate*0.01));
        $b = round($rgb1['b']-($rgb1['b']-$rgb2['b']) * (1-$rate*0.01));
        return rgbToHex("rgb($r,$g,$b)");
    }
}


