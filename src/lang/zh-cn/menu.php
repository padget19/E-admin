<?php
return [
    'add'=>'添加菜单',
    'title'=>'系统菜单管理',
    'fields'=>[
        'top'=>'顶级菜单',
        'pid'=>'上级菜单',
        'name'=>'菜单名称',
        'url'=>'菜单链接',
        'icon'=>'菜单图标',
        'sort'=>'排序',
        'status'=>'状态',
        'super_status'=>'超级管理员状态',
    ],
    'options'=>[
        'admin_visible'=>[
            [1 => '显示'], 
            [0 => '隐藏']
        ]
    ],
    'titles'=>[
        'index'=>'首页',
        'system_manage'=>'系统管理',
        'system_menu_manage'=>'系统菜单管理',
        'system_config'=>'系统配置',
        'system_param_config'=>'系统参数配置',
        'system_user_manage'=>'系统用户管理',
        'auth_manage'=>'权限管理',
        'access_auth_manage'=>'访问权限管理',
        'backup'=>'数据库备份',
    ]
];
