<?php

return [
    // 默认磁盘
    'default' => env('filesystem.driver', 'local'),
    // 磁盘列表
    'disks'   => [
        'safe'  => [
            'type' => 'local',
            'root' => app()->getRootPath() . 'safe',
        ],
        'local' => [
            // 磁盘类型
            'type'       => 'local',
            // 磁盘路径
            'root'       => app()->getRootPath() . 'public/upload',
            // 磁盘路径对应的外部URL路径
            'url'        => '/upload',
            // 可见性
            'visibility' => 'public',
        ],
        //七牛云
        'qiniu'=>[
            'type'=>'qiniu',
            'accessKey'=>'',
            'secretKey'=>'',
            'bucket'=>'togy',
            'domain'=>'https://imgs.my8m.com'
        ],

        //阿里云
        'oss'=>[
            'type'=>'oss',
            'accessKey'=>'',
            'secretKey'=>'',
            'bucket'=>'',
            'domain'=>'http://rockys.oss-cn-hangzhou.aliyuncs.com',
            'endpoint'=>'oss-cn-hangzhou.aliyuncs.com',
            'region'=>'oss-cn-hangzhou',
        ],
        // 更多的磁盘配置信息
    ],
];
