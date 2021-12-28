<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-08-09
 * Time: 16:49
 */

namespace Eadmin\service;

use Composer\Autoload\ClassLoader;
use Eadmin\component\basic\Button;
use Eadmin\PlugServiceProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Exception\RequestException;
use think\App;
use think\facade\Cache;
use think\facade\Console;
use think\facade\Db;
use think\facade\Request;
use think\helper\Arr;

class PlugService
{
    /**
     * 插件基础目录
     * @var string
     */
    protected $plugPathBase = '';
    /**
     * 插件目录集合
     * @var array
     */
    protected $plugPaths = [];
    protected $plugs = [];
    protected static $loader;
    /**
     * 插件服务集合
     * @var array
     */
    protected $serviceProvider = [];
    protected $client;
    protected $loginKey = '';
    protected $table = 'system_plugs';
    public function __construct()
    {
        $this->initialize();;
    }
    protected function initialize()
    {
        $this->app  = app();
        $this->client = new Client([
            'base_uri' => 'https://eadmin.togy.com.cn/api/',
            'verify' => false,
        ]);
        $this->plugPathBase = app()->getRootPath() . config('admin.extension.dir', 'eadmin-plugs');

        foreach (glob($this->plugPathBase . '/*') as $file) {

            if (is_dir($file)) {
                foreach (glob($file . '/*') as $file) {
                    $this->plugPaths[] = $file;
                }
            }
        }

        $this->loginKey = md5(Request::header('Authorization').'plug');
    }

    /**
     * 是否登录
     * @return bool
     */
    public function isLogin(){
        return Cache::has($this->loginKey);
    }
    /**
     * 登录
     * @param string $username 账号
     * @param string $password 密码
     * @return mixed
     */
    public function login($username,$password){
        $response = $this->client->post('plugs/login',[
            'form_params'=>[
                'username'=>$username,
                'password'=>$password,
            ]
        ]);
        $content = $response->getBody()->getContents();
        $res = json_decode($content, true);
        if($res['code'] == 200){
            Cache::set($this->loginKey,$res['data'],60*60*24);
            return true;
        }else{
            return false;
        }
    }
    /**
     * 获取插件目录
     * @return string
     */
    public function getPath()
    {
        return $this->plugPathBase;
    }

    /**
     * 获取 composer 类加载器.
     *
     * @return ClassLoader
     */
    public function loader()
    {
        if (!static::$loader) {
            static::$loader = include $this->app->getRootPath() . '/vendor/autoload.php';
        }
        return static::$loader;
    }

    /**
     * 注册扩展
     */
    public function register()
    {
        $loader = $this->loader();
        $plugs = [];
        foreach ($this->plugPaths as $plugPaths) {
            $file = $plugPaths . DIRECTORY_SEPARATOR . 'composer.json';
            if (is_file($file)) {
                $arr = $this->parseComposer($file);
                $plugs[] = $arr;
            }
        }
        $names = array_column($plugs, 'name');

        try {
            $plugNames = Db::name($this->table)->whereIn('name', $names)->where('status', 1)->column('name');
        } catch (\Exception $exception) {
            $plugNames = [];
        }
        foreach ($this->plugPaths as $plugPaths) {
            $file = $plugPaths . DIRECTORY_SEPARATOR . 'composer.json';
            if (is_file($file)) {
                $arr = $this->parseComposer($file);
                $arr['plug_path'] = $plugPaths;
                $psr4 = Arr::get($arr, 'autoload.psr-4');
                $name = Arr::get($arr, 'name');
                if (in_array($name, $plugNames)) {
                    if ($psr4) {
                        foreach ($psr4 as $namespace => $path) {
                            $path = $plugPaths . '/' . trim($path, '/') . '/';
                            $loader->addPsr4($namespace, $path);
                        }
                    }
                    $serviceProvider = Arr::get($arr, 'extra.e-admin');

                    if ($serviceProvider) {
                        $configPath = $plugPaths . DIRECTORY_SEPARATOR . 'src'.DIRECTORY_SEPARATOR . 'config.php';
                        $this->app->register($serviceProvider);
                        $service = $this->app->getService($serviceProvider);
                        $this->serviceProvider[$name] = $service;
                        $this->app->bind($serviceProvider,$service);
                        if(method_exists($service,'withComposerProperty')){
                            $service->withComposerProperty($arr);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $file
     * @return mixed
     */
    protected function parseComposer($file){
        return json_decode(file_get_contents($file), true);
    }

    /**
     * 获取注册插件服务
     * @param null $name 插件名称
     * @return PlugServiceProvider
     */
    public function getServiceProviders($name = null){
        if(empty($name)){
            return $this->serviceProvider;
        }
        return $this->serviceProvider[$name];
    }
    public function getCate()
    {
        $response = $this->client->get("Plugs/cate");
        $content = $response->getBody()->getContents();
        $content = json_decode($content, true);
        return $content['data'];
    }

    /**
     * 获取所有插件
     * @param string $search 搜索的关键词
     */
    public function all($search = '', $cate_id = 0, $page = 1, $size = 20, $names = null)
    {
        $response = $this->client->get("plugs/list", [
            'query' => [
                'cate_id' => $cate_id,
                'page' => $page,
                'size' => $size,
                'search' => $search,
                'names' => $names,
            ]
        ]);

        $content = $response->getBody()->getContents();
        $content = json_decode($content, true);
        $plugs = $content['data']['data'];
        $delNames = [];
        foreach ($plugs as &$plug) {
            $status = $this->getInfo($plug['composer'], 'status');
            if(isset($this->serviceProvider[$plug['composer']])){
                $service = $this->serviceProvider[$plug['composer']];
                if(method_exists($service,'setting')){
                    $plug['setting'] = app()->invoke([$service,'setting']);
                }
            }
            $plug['status'] = $status ?? false;
            $plug['install_version'] = $this->getInfo($plug['composer'], 'version');
            $plug['install'] = is_numeric($status) ? true : false;
            $plug['path'] = $this->plugPathBase . '/' . $plug['composer'];
            $this->plugs[] = $plug;
            if (!is_dir($plug['path'])) {
                $plug['status'] = false;
                $delNames[] = trim($plug['composer']);
            };
        }
        Db::name($this->table)->whereIn('name', $delNames)->delete();
        return [
            'list'=>$this->plugs,
            'total'=>$content['data']['total']
        ];
    }

    /**
     * 已安装插件
     * @param string $search 搜索的关键词
     * @return array
     */
    public function installed($search = '', $page = 1, $size = 20)
    {
        $names = Db::name($this->table)->column('name');
        if (count($names) == 0) {
            return  [
                'list'=>[],
                'total'=>0
            ];
        }
        $installedPlugs = $this->all($search, 0, $page, $size, $names)['list'];

        foreach ($this->plugPaths as $plugPaths) {
            $file = $plugPaths . DIRECTORY_SEPARATOR . 'composer.json';
            if (is_file($file)) {
                $arr = json_decode(file_get_contents($file), true);
                $plugs[] = $arr;
            }
        }
        $onlinePlugNames = array_column($installedPlugs, 'composer');
        $plugnames = array_column($plugs, 'name');
        $names = array_diff($plugnames, $onlinePlugNames);
        foreach ($names as $name) {
            $index = array_search($name,$plugnames);
            $plug = [];
            $status = $this->getInfo($name, 'status');
            $plug['name']=  $plugs[$index]['description'];
            $plug['desc']=  '';
            $plug['status'] = $status ?? false;
            $plug['install_version'] = '本地插件';
            $plug['install'] = is_numeric($status) ? true : false;
            $plug['path'] = $this->plugPathBase . '/' . $name;
            $plug['composer'] = $name;
            $plug['version'] = [
                [
                    'version'=>'本地插件',
                    'desc'=>'',
                    'require'=>[]
                ]
            ];
            if($plug['install']){
                $installedPlugs[] = $plug;
            }
        }
        return  [
            'list'=>$installedPlugs,
            'total'=>count($installedPlugs)
        ];
    }

    /**
     * 插件状态
     * @param string $name 插件名称
     * @param string $field 字段
     * @return mixed
     */
    public function getInfo($name, $field = 'status')
    {
        try {
            return Db::name($this->table)->where('name', $name)->cache(60)->value($field,null);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * 启用禁用
     * @param string $name 插件名称
     * @param int $status 状态
     * @return int
     * @throws \think\db\exception\DbException
     */
    public function enable($name, $status)
    {
        Db::name('system_menu')->where('mark',$name)->update(['status' => $status]);
        return Db::name($this->table)->where('name', $name)->update(['status' => $status]);
    }

    /**
     * 校验扩展包内容是否正确.
     *
     * @param string $directory
     *
     * @return bool
     */
    protected function checkFiles($directory)
    {
        if (
            !is_dir($directory . '/src')
            || !is_file($directory . '/composer.json')
        ) {
            return false;
        }
        return true;
    }

    /**
     * 执行命令
     * @param string $cmd 命令
     * @param string $path 路径
     * @return bool
     */
    protected function dataMigrate($cmd, $path)
    {
        $migrations = $path . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';
        if (is_dir($migrations)) {
            Console::call('migrate:eadmin', ['cmd' => $cmd, 'path' => $migrations]);
        }

        return true;
    }
    protected function loginSession(){
        $cookies = Cache::get($this->loginKey);
        $cookieJar = new CookieJar();
        foreach ($cookies as $cookie){
            $cookieJar->setCookie(new SetCookie($cookie));
        }
        return $cookieJar;
    }
    /**
     * 安装
     * @param string $name 插件名称
     * @param string $path 插件目录
     * @param string $version 版本
     * @return mixed
     */
    public function install($name, $path, $version)
    {

        try {
            $client = new Client(['verify' => false]);
            $plugZip = app()->getRootPath() . 'plug' . time() . '.zip';
            $client->get($path, [
                'cookies'=>$this->loginSession(),
                'save_to' => $plugZip
            ]);
            $zip = new \ZipArchive();
            if ($zip->open($plugZip) === true) {
                $path = $this->plugPathBase . '/' . $name;
                if (!is_dir($path)) {
                    mkdir($path, 0755, true);
                }
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    if ($i > 0) {
                        $filename = $zip->getNameIndex($i);
                        $pathArr = explode('/', $filename);
                        array_shift($pathArr);
                        $pathName = implode('/', $pathArr);
                        $fileInfo = pathinfo($filename);
                        $toFile = $path . '/' . $pathName;
                        if (isset($fileInfo['extension'])) {
                            copy("zip://" . $plugZip . "#" . $filename, $toFile);
                        } else {
                            if (!is_dir($toFile)) mkdir($toFile, 0755, true);
                        }
                    }
                }
                //关闭
                $zip->close();
                unlink($plugZip);
                $this->dataMigrate('run', $path);
                $seed = $path . '/src/database' . DIRECTORY_SEPARATOR . 'seeds';
                if (is_dir($seed)) {
                    Console::call('seed:eadmin', ['path' => $seed]);
                }

                //插件注册
                Db::name($this->table)->insert([
                    'name' => trim($name),
                    'version' => $version
                ]);
                $this->initialize();
                $this->register();
                //添加菜单
                $file = $path. DIRECTORY_SEPARATOR . 'composer.json';
                $serviceProvider = $this->getServiceProviders($name);
                $serviceProvider->addMenus();
                return true;
            } else {
                return false;
            }
        } catch (RequestException $exception) {
            return false;
        }
    }

    /**
     * 卸载
     * @param string $name 插件名称
     * @param string $path 插件路径
     */
    public function uninstall($name, $path)
    {
        $this->dataMigrate('rollback', $path);
        FileSystemService::instance()->delFiels($path);
        Db::name($this->table)->where('name', $name)->delete();
        Db::name('system_menu')->where('mark', $name)->delete();
        Db::name('system_config')->where('mark', $name)->delete();
        Db::name('system_config_cate')->where('mark', $name)->delete();
        return true;
    }
}
