<?php

namespace Eadmin;

use Closure;
use Eadmin\form\drive\File;
use Eadmin\form\Form;
use Eadmin\model\SystemMenu;
use Eadmin\service\PlugService;
use think\App;
use think\facade\Request;
use think\helper\Arr;
use \think\Service;

abstract class PlugServiceProvider extends Service
{
    protected $composerProperty;

    abstract public function menus();

    abstract public function setting();

    /**
     * 判断扩展是否启用.
     *
     * @return bool
     */
    final public function enabled()
    {
        return Admin::plug()->getInfo($this->getName(), 'status') ? true : false;
    }

    /**
     * 判断扩展是否禁用.
     *
     * @return bool
     */
    final public function disabled()
    {
        return !$this->enabled();
    }

    /**
     * 获取扩展路径
     * @return mixed
     */
    final public function getPath()
    {

        return $this->composerProperty['plug_path'];
    }

    /**
     * 获取扩展名称
     * @return mixed
     */
    final public function getName()
    {
        return $this->composerProperty['name'];
    }

    final public function withComposerProperty($composerProperty)
    {
        $this->composerProperty = $composerProperty;
    }

    /**
     * 获取命名空间
     * @return string
     */
    final public function getNamespace()
    {
        $psr = $this->composerProperty['autoload']['psr-4'];
        $psr = array_flip($psr);
        return end($psr);
    }

    /**
     * 获取或保存配置.
     * @param string $key
     * @param string $value
     * @return array|\ArrayAccess|false|int|mixed
     */
    final public static function config($key, $value = null)
    {
        $file = static::instance()->getPath() . '/src/config.php';
        $data = include $file;
        if (is_null($value)) {
            return Arr::get($data, $key);
        }
        Arr::set($data, $key, $value);
        $content = var_export($data, true);
        $content = <<<PHP
<?php
return $content;
PHP;
        return file_put_contents($file, $content);
    }

    /**
     * 获取自身实例.
     *
     * @return $this
     */
    public static function instance()
    {
        return app(static::class);
    }

    public function boot()
    {
        if ($this->enabled()) {
            //注册路由
            $this->registerRoute();
            $form = $this->setting();
            if($form instanceof Form){
                $form->saved(function () use($form){
                    $callMehod = $form->getCallMethod();
                    $param = $this->app->request->param();
                    if($callMehod['eadmin_class'] == $param['eadmin_class'] && $callMehod['eadmin_function'] == $param['eadmin_function']){
                        //刷新菜单状态
                        $this->refreshMenu();
                    }
                });
            }
        }
    }

    /**
     * 刷新菜单状态
     */
    protected function refreshMenu(){
        $menus = $this->menus();
        if(!empty($menus)){
            foreach ($menus as $menu){
                SystemMenu::where('mark',$this->getName())
                    ->where('name',$menu['name'])
                    ->update(['status'=>$menu['status']]);
            }
        }
    }
    /**
     * 注册路由
     */
    final function registerRoute()
    {
        $dir = basename($this->getPath());
        $this->app->route->group($dir, function () {
            $namespace = $this->getNamespace();
            $pathinfo = strpos(Request::server('REQUEST_URI'), '?') ? strstr(Request::server('REQUEST_URI'), '?', true) : Request::server('REQUEST_URI');
            $pathinfo = ltrim($pathinfo, '/');
            $pathArr = explode('/', $pathinfo);
            if ($pathArr[0] == 'api') {
                $namespace .= 'controller\\api\\';
                $method = Request::method();
                //兼容快捷路由和按请求方式访问
                $function = '';
                $rule = '';
                if (count($pathArr) > 3) {
                    $function = '<function>';
                    $rule = '/';
                }
                $route = '<controller>/' . $method . $function;
                $rule = '<controller>' . $rule . $function;
                $this->app->route->any($rule, $namespace . $route);
            } else {
                $namespace .= 'controller\\';
                $this->app->route->any('<controller>/<function>', $namespace . '<controller>@<function>');
                $this->app->route->any('<controller>', $namespace . '<controller>@index');
            }
        });
    }

    /**
     * 添加菜单
     * @param array $menus
     */
    final function addMenus()
    {
        $menus = $this->menus();
        if (!empty($menus)) {
            foreach ($menus as $key => $menu) {
                if (isset($menu['pid'])) {
                    $menu['pid'] = SystemMenu::where('mark', $this->getName())
                        ->where('name', $menu['pid'])->value('id');
                }
                if (!isset($menu['sort'])) {
                    $menu['sort'] = SystemMenu::max('sort') + 1;
                }
                $menu['mark'] = $this->getName();
                Admin::menu()->add($menu);
            }
        }
    }
}
