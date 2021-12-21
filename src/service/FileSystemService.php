<?php


namespace Eadmin\service;


use Eadmin\Service;
use Symfony\Component\Finder\Finder;
use think\facade\Filesystem;

class FileSystemService extends Service
{
    protected $path = '';
    public function __construct()
    {
        $this->path = Filesystem::disk('local')->path('/');
    }
    public function getPath(){
        return $this->path;
    }
    public function getFiles($path='',$search=''){
        if(empty($path)){
            $path = $this->path;
        }
        $finder = new Finder();
        $datas = [];
        $sort = function (\SplFileInfo $a, \SplFileInfo $b)
        {
            return strcmp($b->getATime(), $a->getATime());
        };
        foreach ($finder->in($path)->depth('== 0')->name("*$search*")->sort($sort) as $file) {
			if($file){
				$urlPath = str_replace($this->path,'',$file->getPathname());
				$author = '';
				if(function_exists('posix_getpwuid')){
					$author = posix_getpwuid($file->getOwner())['name'];
				}
				$datas[] = [
					'name'=>$file->getFilename(),
					'url'=> FileService::instance()->url($urlPath, 'local'),
					'download'=> app()->request->domain().'/eadmin/download?filename='.$file->getFilename().'&path='.$file->getPathname(),
					'path'=>$file->getPathname(),
					'dir'=>$file->isDir(),
					'size'=>$file->getSize(),
					'permission'=>substr(base_convert($file->getPerms(),10,8),-3),
					'author'=>$author,
					'update_time'=>date('Y-m-d H:i:s',$file->getATime()),
				];
			}
        }
        return $datas;
    }

    /**
     * 删除文件
     * @param array|string $path
     * @return bool
     */
    public function delFiels($path){
        $filesystem = new \Symfony\Component\Filesystem\Filesystem;
        $filesystem->remove($path);
        return true;
    }

}
