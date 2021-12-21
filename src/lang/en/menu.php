<?php
return [
    'add' => 'Add',
    'title' => 'System menu management',
    'fields' => [
        'top' => 'Top menu',
        'pid' => 'Superior menu',
        'name' => 'name',
        'url' => 'url',
        'icon' => 'icon',
        'sort' => 'sort',
        'status' => 'status',
        'super_status' => 'Super administrator status',
    ],
    'options' => [
        'admin_visible' => [
            [1 => 'show'],
            [0 => 'hidden']
        ]
    ],
    'titles' => [
        'index' => 'Index',
        'system_manage' => 'System',
        'system_menu_manage' => 'Menu',
        'system_config' => 'System config',
        'system_param_config' => 'System config',
        'system_user_manage' => 'Users',
        'auth_manage' => 'User',
        'access_auth_manage' => 'Permissions',
        'backup' => 'Database backup',
    ]
];
