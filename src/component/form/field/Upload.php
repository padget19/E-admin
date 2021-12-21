<?php


namespace Eadmin\component\form\field;


use Eadmin\Admin;
use Eadmin\component\basic\Button;
use Eadmin\component\form\Field;

use Overtrue\Flysystem\Qiniu\Plugins\UploadToken;
use think\facade\Filesystem;
use think\helper\Str;

/**
 * 上传
 * Class Upload
 * @link https://element-plus.gitee.io/#/zh-CN/component/upload
 * @method $this isUniqidmd5(bool $value = true) 唯一文件名
 * @method $this displayType(string $value) 上传显示方式 image图片, file文件
 * @method $this drag(bool $value = true) 是否启用拖拽上传
 * @method $this multiple(bool $id = true) 多文件上传
 * @method $this limit(int $num) 最大允许上传个数
 * @method $this finder(bool $value = true) finer文件管理
 * @method $this chunk(bool $value = true) 本地分片上传
 * @method $this url(string $value) 上传url
 * @method $this params(array $value) 上传参数
 * @package Eadmin\component\form\field
 */
class Upload extends Field
{
    protected $name = 'EadminUpload';

    public function __construct($field = null, string $value = '')
    {
        parent::__construct($field, $value);
        $this->attr('url', '/eadmin/upload');
        $this->attr('token', Admin::token()->get());
        $uploadType = config('admin.uploadDisks');
        $this->disk($uploadType);
    }



    /**
     * 上传存储类型
     * @param string $diskType local,qiniu,oss
     */
    public function disk($diskType)
    {
        $config          = config('filesystem.disks.' . $diskType);
        $uptype          = $config['type'];
        $this->attr('upType', $diskType);
        if ($uptype == 'qiniu') {
            $this->attr('bucket', $config['bucket']);
            $this->attr('domain', $config['domain']);
            Filesystem::disk('qiniu')->addPlugin(new UploadToken());
            $this->attr('uploadToken', Filesystem::disk('qiniu')->getUploadToken(null, 3600 * 3));
        } elseif ($uptype == 'oss') {
            $this->attr('accessKey', $config['accessKey']);
            $this->attr('secretKey', $config['secretKey']);
            $this->attr('bucket', $config['bucket']);
            $this->attr('endpoint', $config['endpoint']);
            $this->attr('domain', $config['domain']);
            $this->attr('region', $config['region']);
        }
        return $this;
    }

    /**
     * 指定保存目录
     * @param string $path 目录地址
     */
    public function saveDir($path)
    {
        if (substr($path, -1) != '/') {
            $path .= '/';
        }
        $this->attr('saveDir', $path);
        return $this;
    }

    /**
     * 显示尺寸
     * @param int $width 宽度
     * @param int $height 高度
     * @return $this
     */
    public function size($width, $height)
    {
        $this->attr('width', $width);
        $this->attr('height', $height);
        return $this;
    }

    /**
     * 图片建议提示
     * @param int $width 宽度
     * @param int $height 高度
     */
    public function helpSize($width,$height){
        $this->help("建议上传图片尺寸 $width * $height");
        return $this;
    }

    /**
     * 限制文件上传大小
     * @param $value
     * @return $this
     */
    public function fileSize($value){
        $this->attr('fileSizeText',$value);
        $value = str_replace('b','',strtolower($value));
        $pow = 1;
        if(strpos($value,'k')){
            $pow =  pow(1024,1);
        }elseif (strpos($value,'m')){
            $pow =  pow(1024,2);
        }elseif (strpos($value,'g')){
            $pow =  pow(1024,3);
        }elseif (strpos($value,'t')){
            $pow =  pow(1024,4);
        }
        $value = str_replace(['k','m','g','t'],'',$value);
        //字节
        $value = $value * $pow;
        $this->attr('fileSize',$value);
        return $this;
    }
    /**
     * 限制上传类型
     * @param string|array $val
     */
    public function ext($val)
    {
        if (is_string($val)) {
            $val = explode(',', $val);
        }
        $val   = array_map(function ($item) {
            return ".{$item}";
        }, $val);
        $accept = implode(',', $val);
        $this->attr('accept', $accept);
        return $this;
    }
    public function jsonSerialize()
    {
        if($this->attr('upType') === 'local' && is_null($this->attr('finder'))){
            $filesystem = Admin::dispatch('/filesystem');
            $uploadButton = clone $this;
            $uploadButton->finder(false)
                ->attr('foreverShow', true)
                ->disk('local')
                ->content(
                    Button::create('上传')
                        ->icon('el-icon-upload')
                        ->sizeMini()
                );
            $uploadButton->bindValue('', 'modelValue', null);
            $filesystem->attr('upload',$uploadButton);
            $this->attr('finder',$filesystem);
        }
        return parent::jsonSerialize();
    }
}
