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
use Eadmin\controller\Crontab;
use Eadmin\controller\FileSystem;
use Eadmin\controller\ResourceController;
use Eadmin\controller\Queue;
use Eadmin\facade\Schedule;
use Eadmin\middleware\Response;
use Eadmin\model\SystemFile;
use Eadmin\service\BackupData;
use Eadmin\service\MenuService;
use Eadmin\service\QueueService;
use Symfony\Component\Finder\Finder;
use think\facade\Console;
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
        //json压缩
        $this->zlib();
        $this->registerService();
        //注册上传路由
        FileService::instance()->registerRoute();
        //注册插件
        Admin::plug()->register();
        //视图路由
        Admin::registerRoute();
        //权限中间件
        $this->app->middleware->route(\Eadmin\middleware\Permission::class);

    }
    //检测静态文件版本发布
    protected function publishVersion(){
        $file = __DIR__.'/../.env';
        $systemEnv = Env::load($file);
        $envFile = app()->getRootPath().'public/eadmin/.env';
        if(is_file($file)){
            $env =  Env::load($envFile);
            //版本检测
            if($systemEnv->get('VERSION') != $env->get('VERSION')){
                Console::call('eadmin:publish',['-f','-p']);
                $env->set('VERSION',$systemEnv->get('VERSION'));
                $env->save($envFile);
            }
            //主题色切换
            $color = config('admin.theme.color','#409EFF');
            if($color != $env->get('THEME_COLOR','#409EFF')){
                $finder = new Finder();
                $dir = app()->getRootPath().'public/eadmin/static/';
                foreach ($finder->in($dir)->name(['*.css','*.js']) as $file) {
                    $filePath = $file->getRealPath();
                    $content = file_get_contents($filePath);
                    $theme = $env->get('THEME_COLOR');
                    $themeRgb= hex2rgba($theme);
                    $themeRgb = implode(',',$themeRgb);
                    $rgb= hex2rgba($color);
                    $rgb = implode(',',$rgb);
                    $findArr = [$theme,$themeRgb];
                    $replaceArr = [$color,$rgb];
                    for ($i=10;$i<=90;$i+=10){
                        $findArr[] = color_mix('#FFFFFF',$theme,$i);
                        $replaceArr[] = color_mix('#FFFFFF',$color,$i);
                    }
                    $findArr[] = color_mix('#000000',$theme,10);
                    $replaceArr[] = color_mix('#000000',$color,10);
                    $content = str_ireplace($findArr,$replaceArr,$content);
                    $res = file_put_contents($filePath,$content);
                }
                $env->set('THEME_COLOR',$color);
                $env->save($envFile);
            }
        }
    }
    protected function zlib(){
        header("Access-Control-Allow-Origin:*");
        if (extension_loaded('zlib')) {
            if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) and strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) {
                ob_start('ob_gzhandler');
            }
        }
    }
    protected function registerService()
    {
        $this->app->bind([
            'admin.plug'         => PlugService::class,
            'admin.menu'         => MenuService::class,
            'admin.message'      => Message::class,
            'admin.notification' => Notification::class,
        ]);
    }
    protected function crontab(){
        try{
            Schedule::call('数据库备份和定时清理excel目录',function () {
                //数据库备份
                if(sysconf('databackup_on') == 1){
                    BackupData::instance()->backup();
                    $list = BackupData::instance()->getBackUpList();
                    if(count($list) > sysconf('database_number')){
                        $backData = array_pop($list);
                        BackupData::instance()->delete($backData['id']);
                    }
                }
                //定时清理excel目录
                $fileSystem = new \Symfony\Component\Filesystem\Filesystem();
                $fileSystem->remove(app()->getRootPath().'public/upload/excel');
            })->everyDay(sysconf('database_day'));
            Schedule::call('清理上传已删除文件',function () {
                FileService::instance()->clear();
            })->everyMinute();
        }catch (\Exception $exception){

        }
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
            'Eadmin\command\Crontab',
        ]);
        //定时任务
        $this->crontab();
        //检测静态文件版本发布
        $this->publishVersion();
    }
}
