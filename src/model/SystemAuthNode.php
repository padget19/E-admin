<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-25
 * Time: 21:02
 */

namespace Eadmin\model;


use think\facade\Cache;
use think\Model;

class SystemAuthNode extends BaseModel
{
    // 插入后回调
    public static function onAfterInsert(Model $model)
    {
        Cache::tag('eadmin_permissions')->clear();
    }

    // 更新后回调
    public static function onAfterUpdate(Model $model)
    {
        Cache::tag('eadmin_permissions')->clear();
    }
}
