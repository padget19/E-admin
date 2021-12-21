<?php


namespace Eadmin\model;


use think\facade\Cache;
use think\Model;
use think\model\Pivot;

class SystemUserAuth extends Pivot
{
    // 插入前回调
    public static function onAfterInsert(Model $model)
    {
        Cache::tag('eadmin_permissions')->clear();
    }

    // 更新前回调
    public static function onAfterUpdate(Model $model)
    {
        Cache::tag('eadmin_permissions')->clear();
    }
}
