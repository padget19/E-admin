<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-05-23
 * Time: 11:01
 */

namespace Eadmin\component\form\field;


use Eadmin\Admin;
use Eadmin\component\form\Field;
use Overtrue\Flysystem\Qiniu\Plugins\UploadToken;
use think\facade\Filesystem;

/**
 * @method options(array $value) 配置选项
 * @method tags(array $value) 设置标记元素
 */
class Editor extends Field
{
    protected $name = 'EadminEditor';

    public function __construct($field = null, string $value = '')
    {
        parent::__construct($field, $value);
        $this->attr('url', '/eadmin/upload');
        $this->attr('token', Admin::token()->get());
        $this->disk(config('admin.uploadDisks'));
    }

    /**
     * 上传存储类型
     * @param string $diskType local,qiniu,oss
     */
    public function disk($diskType)
    {
        $config = config('filesystem.disks.' . $diskType);
        $uptype = $config['type'];
        $accessKey = '';
        $accessKeySecret = '';
        $this->attr('upType', $uptype);
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
        } else {
            $this->attr('domain', request()->domain());
        }
        return $this;
    }

    /**
     * 高度
     * @param int $number
     */
    public function height($number)
    {
        $this->attr('height', $number);
        return $this;
    }

    /**
     * 宽度
     * @param int $number
     */
    public function width($number)
    {
        $this->attr('width', $number);
        return $this;
    }

    /**
     * textarea模式
     * @param int $height 高度
     * @return $this
     */
    public function textarea($height = 150)
    {
        $this->height($height);
        $this->options([
            'menubar' => false,
            'toolbar' => false,
            'statusbar' => false,
        ]);
        return $this;
    }

    /**
     * 编辑器自定义选项
     * @param string $tool
     */
    public function toolbar($tool = 'undo redo | styleselect | bold italic |
				alignleft | aligncenter | alignright | bullist | numlist | outdent | indent | removeformat | subscript | superscript
				fontsize_formats | cut | copy | paste | forecolor')
    {
        $this->attr('toolbar', $tool);
        return $this;
    }
}
