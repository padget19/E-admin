<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-02-16
 * Time: 00:00
 */

namespace Eadmin\component\basic;

use Eadmin\component\Component;

/**
 * Class DownloadFile
 * @package Eadmin\component\basic
 * @method filename(string $filename) 文件名
 * @method url(string $url) 文件链接
 */
class DownloadFile extends Component
{
    protected $name = 'EadminDownloadFile';
    /**
     * 创建
     * @return DownloadFile
     */
    public static function create()
    {
        return new self();
    }
}
