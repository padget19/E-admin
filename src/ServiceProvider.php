<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-03-25
 * Time: 21:43
 */

namespace Eadmin;


use Eadmin\component\basic\Message;
use Eadmin\component\basic\Notification;
use Eadmin\controller\FileSystem;
use Eadmin\controller\ResourceController;
use Eadmin\controller\Queue;
use Eadmin\middleware\Response;
use Eadmin\service\MenuService;
use Eadmin\service\QueueService;
use think\facade\Db;
use think\route\Resource;
use think\Service;
use Eadmin\controller\Backup;
use Eadmin\controller\Log;
use Eadmin\controller\Menu;
use Eadmin\controller\Notice;
use Eadmin\controller\Plug;
use Eadmin\middleware\Permission;
use Eadmin\service\FileService;
use Eadmin\service\PlugService;
use Eadmin\service\TableViewService;

class ServiceProvider extends Service
{
    public function register()
    {
        header("Access-Control-Allow-Origin:*");
        if (extension_loaded('zlib')) {
            if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) and strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) {
                ob_start('ob_gzhandler');
            }
        }
        //注册上传路由
        FileService::instance()->registerRoute();
        //注册插件
        PlugService::instance()->register();
        $this->app->middleware->route(\Eadmin\middleware\Permission::class);
        $this->registerView();
        $this->registerService();
    }

    public function registerService()
    {
        $this->app->bind([
            'admin.menu'         => MenuService::class,
            'admin.message'      => Message::class,
            'admin.notification' => Notification::class,
        ]);
    }

    protected function registerView()
    {

        Admin::registerRoute();;

        //菜单管理
        $this->app->route->resource('menu', Menu::class);
        //日志调试
        $this->app->route->post('log/logData', Log::class . '@logData');
        $this->app->route->get('log/debug', Log::class . '@debug');
        $this->app->route->post('log/remove', Log::class . '@remove');
        //插件
        $this->app->route->get('plug/add', Plug::class . '@add');
        $this->app->route->get('plug/grid', Plug::class . '@grid');
        $this->app->route->post('plug/enable', Plug::class . '@enable');
        $this->app->route->post('plug/install', Plug::class . '@install');
        $this->app->route->get('plug', Plug::class . '@index');
        //消息通知
        $this->app->route->get('notice/notification', Notice::class . '@notification');
        $this->app->route->post('notice/system', Notice::class . '@system');
        $this->app->route->post('notice/reads', Notice::class . '@reads');
        $this->app->route->delete('notice/clear', Notice::class . '@clear');
        //数据库备份
        $this->app->route->get('backup/config', Backup::class . '@config');
        $this->app->route->post('backup/add', Backup::class . '@add');
        $this->app->route->post('backup/reduction', Backup::class . '@reduction');
        $this->app->route->get('backup', Backup::class . '@index');
        //文件管理系统
        $this->app->route->get('filesystem', FileSystem::class . '@index');
        $this->app->route->post('filesystem/mkdir', FileSystem::class . '@mkdir');
        $this->app->route->post('filesystem/rename', FileSystem::class . '@rename');
        $this->app->route->delete('filesystem/del', FileSystem::class . '@del');
        //系统队列
        $this->app->route->get('queue/progress',function (){
            $queue = new QueueService($this->app->request->get('id'));
            return json(['code'=>200,'data'=>$queue->progress()]);
        });
        $this->app->route->get('queue', Queue::class . '@index');
        $this->app->route->post('queue/retry', Queue::class . '@retry');
    }

    public function boot()
    {
        $this->commands([
            'Eadmin\command\BuildView',
            'Eadmin\command\Publish',
            'Eadmin\command\Plug',
            'Eadmin\command\Migrate',
            'Eadmin\command\Seed',
            'Eadmin\command\Iseed',
            'Eadmin\command\Install',
            'Eadmin\command\ReplaceData',
            'Eadmin\command\ClearDatabase',
            'Eadmin\command\Queue',
        ]);
    }
}
