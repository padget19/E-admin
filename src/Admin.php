<?php


namespace Eadmin;


use Eadmin\component\Component;
use Eadmin\component\basic\Html;
use Eadmin\component\basic\Message;
use Eadmin\component\basic\Notification;
use Eadmin\controller\Backup;
use Eadmin\controller\Config;
use Eadmin\controller\Crontab;
use Eadmin\controller\FileSystem;
use Eadmin\controller\Log;
use Eadmin\controller\Menu;
use Eadmin\controller\Notice;
use Eadmin\controller\Plug;
use Eadmin\controller\Queue;
use Eadmin\controller\ResourceController;
use Eadmin\service\AuthService;
use Eadmin\service\MenuService;
use Eadmin\service\NodeService;
use Eadmin\service\PlugService;
use Eadmin\service\QueueService;
use Eadmin\service\TokenService;
use think\app\Url;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Request;
use think\facade\Route;
use think\facade\View;
use think\helper\Str;
use think\route\dispatch\Callback;
use think\route\dispatch\Controller;

class Admin
{
    /**
     * 配置系统参数
     * @param string $name 参数名称
     * @param boolean $value 无值为获取
     * @return string|boolean
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function sysconf($name, $value = null)
    {
		if (is_null($value)) {
			$value = Db::name('SystemConfig')->where('name', $name)->value('value');
			if (is_null($value)) {
				return '';
			} else {
				$json = json_decode($value,true);
				if (is_array($json)) {
					return $json;
				}else{
					return $value;
				}
			}
		} else {
            if(is_array($value)){
                $value = json_encode($value,JSON_UNESCAPED_UNICODE);
            }
            $sysconfig = Db::name('SystemConfig')->where('name', $name)->find();
            if ($sysconfig) {
                return Db::name('SystemConfig')->where('name', $name)->update(['value' => $value]);
            } else {
                return Db::name('SystemConfig')->insert([
                    'name' => $name,
                    'value' => $value,
                ]);
            }
        }
    }

    /**
     * 渲染组件
     * @param string $template 模板文件名
     * @param array $vars 模板变量
     * @return Component
     */
    public static function view($template, $vars = [])
    {
        if (is_file($template)) {

            $content = View::display(file_get_contents($template), $vars);
        } else {
            $content = View::fetch($template, $vars);
        }
        return Html::create($content)->tag('component')->attr('key',Str::random(30, 3));
    }

    public static function notification()
    {
        return new Notification();
    }

    public static function message()
    {
        return new Message();
    }

    /**
     * 获取用户角色组
     */
    public static function roles()
    {
        return self::user()->roles;
    }

    /**
     * 获取当前登陆用户id
     * @return string
     */
    public static function id()
    {
        return self::token()->id();
    }

    public static function menus()
    {
        if (self::id() == config('admin.admin_auth_id')) {
            self::user()->menus();
        } else {
            self::user()->menus();
        }

    }

    /**
     * 验证权限节点
     * @param string $class 完整类名
     * @param string $function 方法
     * @param string $method 请求方法
     * @return bool
     */
    public static function check($class, $function, $method = 'get')
    {
        $nodeId = md5($class . $function . strtolower($method));
        $permissions = self::permissions();

        foreach ($permissions as $permission) {
            if ($permission['id'] == $nodeId) {

                if ($permission['is_login']) {
                    self::token()->auth();
                }
                if (!$permission['is_auth']) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 获取权限节点
     * @return mixed
     */
    public static function permissions()
    {

        $permissionsKey = 'eadmin_permissions' . self::id();
        $nodes = Cache::get($permissionsKey);
        if ($nodes) {
            return $nodes;
        }
        $nodes = self::node()->all();
        if (self::id()) {
            $permissions = self::user()->permissions();
            $nodeIds = array_column($permissions, 'node_id');
        } else {
            $nodeIds = [];
        }
        if (self::id() != config('admin.admin_auth_id')) {
            foreach ($nodes as $key => &$node) {
                if (!in_array($node['id'], $nodeIds)) {
                    $node['is_auth'] = false;
                }
            }
        }
        Cache::tag('eadmin_permissions')->set($permissionsKey, $nodes);
        return $nodes;
    }

    /**
     * 获取当前用户
     * @return mixed
     */
    public static function user()
    {
        return self::token()->user();
    }

    public static function token()
    {
        return new TokenService();
    }
    /**
     * 插件
     * @return PlugService
     */
    public static function plug()
    {
        return app('admin.plug');
    }

    /**
     * 权限
     * @return AuthService
     */
    public static function auth(){
        return app('admin.auth');
    }
    /**
     * 菜单服务
     * @return MenuService
     */
    public static function menu()
    {
        return app('admin.menu');
    }

    /**
     * 权限节点
     * @return NodeService
     */
    public static function node()
    {
        return new NodeService();
    }
    
    /**
     * 树形
     * @param array $data 数据
     * @param string $id
     * @param string $pid
     * @param string $children
     * @return array
     */
    public static function tree(array $data, $id = 'id', $pid = 'pid', $children = 'children')
    {
        $items = array();
        foreach ($data as $v) {
            $items[$v[$id]] = $v;
        }
        $tree = array();
        foreach ($items as $k => $item) {
            if (isset($items[$item[$pid]])) {
                $items[$item[$pid]][$children][] = &$items[$k];
            } else {
                $tree[] = &$items[$k];
            }
        }
        return $tree;
    }

    /**
     * 解析url返回路由调度
     * @param $url
     * @return bool|\think\route\Dispatch|null
     */
    public static function getDispatch($url)
    {

        $dispatch = null;
        try {
            if (strpos($url, '/') !== false) {
                if($url instanceof Url){
                    $url->suffix(false);
                }
                $parse = parse_url($url);
                $path = $parse['path'] ?? '';
                $pathinfo = array_filter(explode('/', $path));
                $name = current($pathinfo);
                if(empty(app('http')->getName())){
                    app('http')->name('admin');
                    app()->setNamespace("app\\admin");
                }
                if ($name == app('http')->getName()) {
                    array_shift($pathinfo);
                }
                $url = implode('/', $pathinfo);
                $dispatch = Route::getDomains()['-']->check(request(), $url);
                if ($dispatch === false) {
                    $dispatch = Route::url($url);
                }
            }
        } catch (\Throwable $exception) {

        }
        return $dispatch;
    }

    public static function getDispatchCall($dispatch)
    {
        $eadmin_class = null;
        $eadmin_function = null;
        try {
            $dispatch->init(app());
            if ($dispatch instanceof Controller) {

                list($controller, $eadmin_function) = $dispatch->getDispatch();
                $eadmin_class = get_class($dispatch->controller($controller));
                if (is_null($eadmin_function)) {
                    $eadmin_function = config('route.default_action');
                }
            } elseif ($dispatch instanceof Callback && !($dispatch->getDispatch() instanceof \Closure)) {
                list($eadmin_class, $eadmin_function) = $dispatch->getDispatch();
            }
        } catch (\Exception $exception) {

        }
        return [$eadmin_class, $eadmin_function];
    }


    /**
     * 添加队列任务
     * @param string $title 标题
     * @param string $job 任务
     * @param array $data 数据
     * @param int $delay 延迟时间
     * @return mixed
     */
    public static function queue($title, $job, array $data, $delay = 0)
    {
        $queue = new QueueService();
        return $queue->queue($title, $job, $data, $delay);
    }

    /**
     * 解析url返回query
     * @param $url
     * @return array
     */
    public static function parseUrlQuery($url){
        $vars = [];
        if (is_string($url) || $url instanceof Url) {
            $parse = parse_url($url);
            if (isset($parse['query'])) {
                parse_str($parse['query'], $vars);
            }
        }
        return $vars;
    }
    /**
     * 解析url并执行返回
     * @param mixed $url
     * @return mixed
     */
    public static function dispatch($url)
    {
        $dispatch = Admin::getDispatch($url);
        $vars = self::parseUrlQuery($url);
        $data = $url;
        if ($dispatch) {
            $dispatch->init(app());
            $request = app()->request;
            $get = $request->get();
            $request->withGet($vars);
            if ($dispatch instanceof Controller) {
                list($controller, $action) = $dispatch->getDispatch();
                try {
                    $instance = $dispatch->controller($controller);
                    $reflect = new \ReflectionMethod($instance, $action);
                    $data = app()->invokeReflectMethod($instance, $reflect, $vars);
                } catch (\Exception $exception) {

                }
            } elseif ($dispatch instanceof Callback) {
                $data = app()->invoke($dispatch->getDispatch(), $vars);
            }
            $request->withGet($get);
        }
        if($data instanceof Component){
            return $data;
        }else{
            return $url;
        }
    }

    public static function registerRoute()
    {
        app()->route->resource('eadmin', ResourceController::class)->ext('rest');
        //菜单管理
        app()->route->resource('menu', Menu::class);
        //日志调试
        app()->route->post('log/logData', Log::class . '@logData');
        app()->route->get('log/debug', Log::class . '@debug');
        app()->route->post('log/remove', Log::class . '@remove');
        //插件
        app()->route->get('plug/add', Plug::class . '@add');
        app()->route->get('plug/grid', Plug::class . '@grid');
        app()->route->post('plug/enable', Plug::class . '@enable');
        app()->route->post('plug/install', Plug::class . '@install');
        app()->route->post('plug/uninstall', Plug::class . '@uninstall');
        app()->route->post('plug/uploadGit', Plug::class . '@uploadGit');
        app()->route->get('plug', Plug::class . '@index');
        //消息通知
        app()->route->get('notice/notification', Notice::class . '@notification');
        app()->route->post('notice/system', Notice::class . '@system');
        app()->route->post('notice/reads', Notice::class . '@reads');
        app()->route->delete('notice/clear', Notice::class . '@clear');
        //数据库备份
        app()->route->get('backup/config', Backup::class . '@config');
        app()->route->post('backup/add', Backup::class . '@add');
        app()->route->post('backup/reduction', Backup::class . '@reduction');
        app()->route->get('backup', Backup::class . '@index');
        //文件管理系统
        app()->route->get('filesystem', FileSystem::class . '@index');
        app()->route->post('filesystem/mkdir', FileSystem::class . '@mkdir');
        app()->route->post('filesystem/rename', FileSystem::class . '@rename');
        app()->route->delete('filesystem/del', FileSystem::class . '@del');
        app()->route->post('filesystem/moveCate', FileSystem::class . '@moveCate');
        //系统队列
        app()->route->get('queue/progress',function (){
            $queue = new QueueService(app()->request->get('id'));
            return json(['code'=>200,'data'=>$queue->progress()]);
        });
        app()->route->get('queue', Queue::class . '@index');
        app()->route->post('queue/retry', Queue::class . '@retry');
        //定时任务
        app()->route->any('crontab/clear', Crontab::class . '@clear');
        app()->route->any('crontab/exec', Crontab::class . '@exec');
        app()->route->get('crontab', Crontab::class . '@index');
        //系统开发配置
        app()->route->get('system_config', Config::class . '@index');
    }
}
