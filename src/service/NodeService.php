<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-23
 * Time: 22:44
 */

namespace Eadmin\service;

use Eadmin\Admin;
use Eadmin\PlugServiceProvider;
use Symfony\Component\Finder\Finder;
use think\route\Resource;
use Eadmin\Service;


/**
 * 系统节点服务
 * Class NodeService
 * @package Eadmin\service
 */
class NodeService
{
    //节点缓存key
    protected $cacheKey = 'eadmin_node_list';
    protected $treeArr = [];

    public function all()
    {

        if (app()->cache->has($this->cacheKey) && !env('APP_DEBUG')) {

            return app()->cache->get($this->cacheKey);
        } else {
            $files = $this->getControllerFiles();
            $data = $this->parse($files);
            app()->cache->set($this->cacheKey, $data);

            return $data;
        }
    }

    //树形格式
    public function tree()
    {
        $files = $this->getControllerFiles();
        $this->parse($files);
        $data = [];
        foreach ($this->treeArr as $tree) {
            $data[] = $tree;
        }
        return $data;
    }

    /**
     * 解析注释
     * @param mixed $doc 注释
     * @param PlugServiceProvider $plug 插件
     * @return array|bool
     */
    protected function parseDocComment($doc,$plug = null)
    {
        if (preg_match('#^/\*\*(.*)\*/#s', $doc, $comment) === false) {
            return false;
        }
        if (!isset($comment[1])) {
            return false;
        }
        $comment = trim($comment [1]);
        if (preg_match_all('#^\s*\*(.*)#m', $comment, $lines) === false) {
            return false;
        }
        $commentsLine = end($lines);
        if (count($commentsLine) > 0) {
            $auth = false;
            $login = false;
            $authPlug = true;
            $title = array_shift($commentsLine);
            foreach ($commentsLine as $line) {
                $line = trim($line);
                if (preg_match('/@auth\s*true/i', $line) && $auth == false) {
                    $auth = true;
                } elseif (preg_match('/@login\s*true/i', $line) && $login == false) {
                    $login = true;
                } elseif (preg_match('/@plugConfig\s(.*)/i', $line,$arr) && isset($arr[1]) && $plug) {
                    $field = trim($arr[1]);
                    if(!$plug::config($field)){
                        $authPlug = false;
                    }
                }
            }
        } else {
            return false;
        }
        return [trim($title), $auth, $login,$authPlug];

    }

    /**
     * 解析控制器文件返回权限节点
     * @param $files
     * @throws \ReflectionException
     */
    protected function parse($files)
    {
        $data = [];
        $rules = app()->route->getRuleList();
        foreach ($files as $key => $item) {
            $file = $item['file'];
            $controller = str_replace('.php', '', basename($file));
            if (!empty($item['module'])) {
                $moduleName = $item['module'];
            }
            $namespace = $item['namespace'];
            $plug = $item['plug'] ?? false;
            $class = new \ReflectionClass($namespace);
            $classDoc = $this->parseDocComment($class->getDocComment(),$plug);
            if ($classDoc === false) {
                $title = $controller;
            } else {
                $title = array_shift($classDoc);
                if(empty($title)){
                    $title = $controller;
                }
            }
            $this->treeArr[$moduleName]['children'][$key] = [
                'label' => $title,
                'id' => md5($namespace),
                'children' => []
            ];
            $methodNode = [];
            foreach ($class->getMethods() as $method) {
                $doc = $method->getDocComment();
                $res = $this->parseDocComment($doc,$plug);
                if ($method->class == $namespace && $method->isPublic()) {
                    $action = $method->getName();
                    $reflectionNamedType = $method->getReturnType();
                    if ($res !== false) {
                        list($title, $auth, $login,$authPlug) = $res;
                        $nodeData = [
                            'label' => $title,
                            'class' => $namespace,
                            'action' => $action,
                            'is_auth' => $auth,
                            'is_login' => $login,
                            'method' => 'get',
                            'id' => md5($namespace . $action . 'get'),
                        ];
                        if ($auth && $authPlug) {
                            if ($reflectionNamedType && $reflectionNamedType->getName() == 'Eadmin\form\Form') {
                                $label = $nodeData['label'];
                                $nodeData['label'] = $label . '添加';
                                $nodeData['method'] = 'post';
                                $nodeData['id'] = md5($namespace . $action . $nodeData['method']);
                                $data[] = $nodeData;
                                $methodNode[] = $nodeData;
                                $nodeData['label'] = $label . '修改';
                                $nodeData['method'] = 'put';
                                $nodeData['id'] = md5($namespace . $action . $nodeData['method']);
                                $data[] = $nodeData;
                                $methodNode[] = $nodeData;
                            } else {
                                $data[] = $nodeData;
                                $methodNode[] = $nodeData;
                                if ($reflectionNamedType && $reflectionNamedType->getName() == 'Eadmin\grid\Grid') {
                                    $nodeData['label'] = '删除';
                                    $nodeData['method'] = 'delete';
                                    $nodeData['id'] = md5($namespace . $action . $nodeData['method']);
                                    $data[] = $nodeData;
                                    $methodNode[] = $nodeData;
                                }
                            }
                        }
                    }
                }
            }
            $this->treeArr[$moduleName]['children'][$key]['children'] = $methodNode;
            list($auth, $login,$authPlug) = $classDoc;
            if (count($this->treeArr[$moduleName]['children'][$key]['children']) == 0 || !$authPlug) {
                unset($this->treeArr[$moduleName]['children'][$key]);
            }
            $this->treeArr[$moduleName]['children'] = array_values($this->treeArr[$moduleName]['children']);
        }
        return $data;
    }

    /**
     * 获取所有模块控制器文件
     * @return array
     */
    protected function getControllerFiles()
    {
        $appPath = app()->getBasePath();
        $controllerFiles = [];
        //扫描所有模块
        $modules = [];
        foreach (glob($appPath . '*') as $file) {
            if (is_dir($file)) {
                $modules[] = $file;
            }
        }
        foreach (glob(dirname(__DIR__) . '/controller/' . '*.php') as $file) {
            if (is_file($file)) {
                $controller = str_replace('.php', '', basename($file));
                $namespace = "Eadmin\\controller\\$controller";
                $controllerFiles[] = [
                    'namespace' => $namespace,
                    'module' => 'admin',
                    'file' => $file,
                ];
            }
        }
        $finder = new Finder();
        //扫描存在配置权限模块控制器下所有文件
        foreach ($modules as $module) {
            $moduleName = basename($module);
            //权限模块
            $authModuleName = config('admin.authModule');
            if (isset($authModuleName[$moduleName])) {
                $authModuleTitle = $authModuleName[$moduleName];
                $this->treeArr[$moduleName] = [
                    'label' => $authModuleTitle,
                    'id' => md5($moduleName),
                ];
                foreach ($finder->files()->in($module . '/controller')->name('*.php') as $file) {
                    $controller = str_replace('.php', '', $file->getRealPath());
                    $controller = strstr($controller,'controller'.DIRECTORY_SEPARATOR);
                    $controller = explode(DIRECTORY_SEPARATOR,$controller);
                    $controller = implode('\\',$controller);
                    $namespace = "app\\$moduleName\\$controller";
                    $controllerFiles[] = [
                        'namespace' => $namespace,
                        'module' => $moduleName,
                        'file' => $file,
                    ];
                }
            }
        }
        $serviceProviders = Admin::plug()->getServiceProviders();
        foreach ($serviceProviders as $serviceProvider) {
            if ($serviceProvider instanceof PlugServiceProvider) {
                $reflectionClass = new \ReflectionClass($serviceProvider);
                $module = dirname($reflectionClass->getFileName());
                foreach (glob($module . '/controller/' . '*.php') as $file) {
                    if (is_file($file)) {
                        $controller = str_replace('.php', '', basename($file));
                        $namespace = $serviceProvider->getNamespace() . "controller\\$controller";
                        $controllerFiles[] = [
                            'namespace' => $namespace,
                            'module' => '',
                            'file' => $file,
                            'plug'=>$serviceProvider
                        ];
                    }
                }
            }
        }
        return $controllerFiles;
    }
}
