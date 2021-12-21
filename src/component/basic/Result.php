<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-06-26
 * Time: 01:21
 */

namespace Eadmin\component\basic;


use Eadmin\component\Component;

/**
 * Class Result
 * @package Eadmin\component\basic
 * @method $this status(string $type) 类型    'success' | 'error' | 'info' | 'warning'| '404' | '403' | '500'
 */
class Result extends Component
{
    protected $name = 'AResult';
    public static function create()
    {
        return new self();
    }

    /**
     * 操作区内容
     * @param $content
     * @return Result
     */
    public function extra($content){
        $this->content['extra'] = [];
        return $this->content($content,'extra');
    }
}