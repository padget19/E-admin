<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-09
 * Time: 23:24
 */

namespace app\admin\controller;



use EasyWeChat\Factory;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Validate;
use Eadmin\Controller;
use Eadmin\model\AdminModel;
use Eadmin\service\CaptchaService;
use Eadmin\service\TokenService;
use Eadmin\Admin;

/**
 * 登陆
 * Class Login
 * @package app\admin\controller
 */
class Login extends Controller
{

    /**
     * 登陆
     * @auth false
     * @login false
     */
    public function index()
    {
        if($this->request->isPost()){
            $data = $this->request->post();
            if(!empty($data['wx_code'])){
                $this->wxQrcodeLogin($data['wx_code']);
            }
            $validate = Validate::rule([
                'username' => 'require',
                'password' => 'require|min:5'
            ])->message([
                'username.require' => '登录账号不能为空!',
                'password.require' => '登录密码不能为空！',
                'password.min' => '登录密码长度不能少于4位有效字符！',
            ]);
            $valiRes =  $validate->check($data);
            $username = $data['username'];
            $password = $data['password'];
            if ($valiRes === true) {
                $verifyKey = md5($this->app->request->ip().date('Y-m-d'));
                $usernameVerifyKey = md5($this->app->request->ip().$username.date('Y-m-d'));
                $verifyErrorNum = Cache::get($usernameVerifyKey);
                if($verifyErrorNum >= 10){
                    $this->errorCode(3003);
                }
                $user = AdminModel::where('username', $username)->find();
                if (empty($user) || !password_verify($password,$user['password'])) {
                    $this->app->cache->inc($usernameVerifyKey,1);
                    $this->app->cache->inc($verifyKey,1);
                    $this->errorCode(3001);
                }
                $verifyErrorNum = Cache::get($verifyKey);
                if($verifyErrorNum >= 3 && !CaptchaService::instance()->check($data['verify'],$data['hash'])){
                    $this->errorCode(4003);
                }
                if ($user['status'] == 0) {
                    $this->errorCode(3002);
                }
                //微信openid绑定账号
                if($this->request->has('wxBind') && $this->request->has('openid')){
                    $user->openid = $data['openid'];
                    $user->save();
                }
                $tokens = Admin::token()->encode($user);
                event('UserLogin', $user);
                admin_success_message('登陆成功')->data($tokens);
            } else {
                admin_error_message($validate->getError());
            }
        }else{
           $where= ['web_logo','web_name','web_miitbeian','web_copyright'];
           $data = Db::name('SystemConfig')->whereIn('name',$where)->column('value','name');
           return Admin::view('/login')->attrs([
               'webLogo'=>$data['web_logo'],
               'webName'=>$data['web_name'],
               'webMiitbeian'=>$data['web_miitbeian'],
               'webCopyright'=>$data['web_copyright'],
               'deBug'=>(bool) env('APP_DEBUG')
           ]);
        }
    }
    /**
     * 微信二维码登录
     */
    protected function wxQrcodeLogin($code){
        $config = [
            'app_id'   => sysconf('wechat_open_appid'),
            'secret'   => sysconf('wechat_open_secret'),
        ];
        $needBindWx = false;
        try {
            $app = Factory::officialAccount($config);
            $oauth = $app->oauth;
            $token = $oauth->getAccessToken($code);
            $user = $oauth->user($token);
            $openid =  $user->getId();
            $user = AdminModel::where('openid', $openid)->find();
            if($user){
                event('UserLogin', $user);
                $tokens = Admin::token()->encode($user);
            }else{
                $needBindWx = true;
            }
        }catch (\Exception $e){
            \think\facade\Log::error("微信二维码登录错误:".$e->getMessage());
            $this->successCode(40000,422);
        }
        if($needBindWx){
            $this->successCode($openid,422);
        }else{
            admin_success_message('登陆成功')->data($tokens);
        }
    }
    /**
     * 退出登陆
     * @auth false
     * @login false
     */
    public function logout()
    {
        Admin::token()->logout();
        admin_success_message('成功退出登陆');
    }
}
