<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-25
 * Time: 16:33
 */

use Eadmin\model\AdminModel;

return [
    //超级管理员id
    'admin_auth_id' => 1,
    //token
    'token'=>[
        //令牌key
        'key' => 'QsoYEClMJsgOSWUBkSCq26yWkApqSuH3',
        //令牌过期时间
        'expire' => 86400,
        //是否唯一登陆
        'unique' => false,
        //系统用户模型
        'model' => AdminModel::class,
        //验证字段
        'auth_field'=>[
            'password'
        ]
    ],
    //系统用户表
    'system_user_table' => 'system_user',
    //权限模块
    'authModule' => [
        'admin' => '系统模块',
    ],
    //上传filesystem配置中的disk, local本地,qiniu七牛云,oss阿里云
    'uploadDisks' => 'local',

    //地图
    'map'=>[
        'default' => 'amap',
        //高德地图key
        'amap'=>[
          'api_key'=>'7b89e0e32dc5eb583c067edb5491c4d3'
        ],
    ],
    //开关顶部菜单
    'topMenu'=> true,
    //开关标签菜单（缓存）
    'tagMenu'=> true,
    //主题
    'theme'=>[
        //主题色
        'color'=>'#409EFF'
    ]
];
