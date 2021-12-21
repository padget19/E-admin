<?php

namespace Eadmin\service;

use Eadmin\Admin;
use Eadmin\model\AdminModel;
use Eadmin\model\SystemAuthData;
use Eadmin\model\SystemUserAuth;
use think\db\Query;
use think\facade\Db;
use think\Model;

class AuthService
{
    public function checkDataAuth(array $fields,Query  $query){
        if(Admin::id() && count($fields) > 0){
            $auth_ids = Db::name('SystemUserAuth')
                ->where('user_id',Admin::id())
                ->column('auth_id');
            //权限-数据权限
            //组织
            $groupIds = Db::name('SystemAuthData')
                ->where('auth_type',1)
                ->whereIn('auth_id',$auth_ids)
                ->where('data_type',1)->column('data_id');
            $groupUserIds = Db::name('SystemUserAuth')
                ->whereIn('auth_id',$groupIds)->column('user_id');
            //个人
            $userIds = Db::name('SystemAuthData')->where('auth_type',1)
                ->whereIn('auth_id',$auth_ids)
                ->where('data_type',2)->column('data_id');
            $userAuthIds = array_merge($groupUserIds,$userIds);
            //个人-数据权限

            //组织
            $groupIds = Db::name('SystemAuthData')
                ->where('auth_type',2)
                ->where('auth_id',Admin::id())
                ->where('data_type',1)->column('data_id');
            $groupUserIds = Db::name('SystemUserAuth')
                ->whereIn('auth_id',$groupIds)->column('user_id');
            //个人
            $userIds = Db::name('SystemAuthData')->where('auth_type',2)
                ->where('auth_id',Admin::id())
                ->where('data_type',2)->column('data_id');
            $userAuthIds = array_merge($userAuthIds,$groupUserIds,$userIds);

            $userAuthIds = array_unique($userAuthIds);
            $query->where(function (Query  $query) use($fields,$userAuthIds){
                foreach ($fields as $field){
                    $query->whereOr($query->getTable().'.'.$field,Admin::id());
                    if(count($userAuthIds) > 0){
                        $query->whereIn($query->getTable().'.'.$field,$userAuthIds,'OR');
                    }
                }
            });
        }
    }
}
