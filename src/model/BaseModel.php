<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-05-09
 * Time: 00:15
 */

namespace Eadmin\model;


use app\model\User;
use think\facade\Request;
use think\Model;

/**
 * @method $this pages() 分页条件
 * Class BaseModel
 * @package Eadmin\model
 */
class BaseModel extends Model
{
    protected $autoWriteTimestamp = 'datetime';
    protected $globalScope = ['base'];

    public function scopeBase($query)
    {
        $id          = $query->getPk();
        $tableFields = $query->getTableFields();
        //默认排序
        if (in_array('sort', $tableFields)) {
            $query->order('sort asc')->order("{$id} desc");
        } else {
            $query->order("{$id} desc");
        }
       
    }

    //分页条件
    public function scopePages($query, $page = 1, $size = 10)
    {
        $page = Request::param('page', $page);
        $size = Request::param('size', $size);
        $query->page($page, $size);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }
}
