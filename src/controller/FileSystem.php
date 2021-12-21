<?php


namespace Eadmin\controller;


use Eadmin\Controller;
use Eadmin\service\FileSystemService;

class FileSystem extends Controller
{
    public function index(){
        $data = FileSystemService::instance()->getFiles($this->request->get('path'),$this->request->get('search'));
        $fileSystem = new \Eadmin\component\basic\FileSystem($data);
        return $fileSystem->initPath(FileSystemService::instance()->getPath())->title('资源库')->description('列表');
    }
    //新建文件夹
    public function mkdir($path){
        if(is_dir($path)){
            admin_error_message('文件夹已存在');
        }
        mkdir($path,0755);
        admin_success('成功','新建文件夹成功');
    }
    //重命名文件夹
    public function rename($name,$path){
        if(is_dir($path)){
            $newPath = dirname($path).DIRECTORY_SEPARATOR.$name;
            if(is_dir($newPath)){
                admin_error_message('重命名文件夹名称已存在');
            }
            rename($path,$newPath);
            admin_success('成功','文件夹重命名成功');
        }
        admin_error_message('文件夹不存在');
    }
    public function del($paths){
        FileSystemService::instance()->delFiels($paths);
        admin_success('成功','删除完成');
    }
}
