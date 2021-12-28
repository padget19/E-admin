<?php

namespace app\admin\controller;

use Eadmin\Controller;
use Eadmin\form\drive\Config;
use Eadmin\service\ConfigService;
use think\facade\Cache;

use Eadmin\form\Form;


use Eadmin\service\CaptchaService;
use think\facade\Db;


/**
 * 系统参数配置
 * Class System
 * @package app\admin\controller
 */
class System extends Controller
{
    /**
     * 系统参数配置
     * @auth true
     * @login true
     * @return string
     */
    public function config()
    {
        return ConfigService::instance()->form();
    }

    /**
     * 获取验证码
     */
    public function verify()
    {
        $verify = CaptchaService::instance()->create();
        $verifyKey = md5($this->app->request->ip() . date('Y-m-d'));
        $verifyErrorNum = Cache::get($verifyKey);
        if ($verifyErrorNum >= 3) {
            $verify['mode'] = 2;
        } else {
            $verify['mode'] = 1;
        }
        $this->successCode($verify);
    }


}
