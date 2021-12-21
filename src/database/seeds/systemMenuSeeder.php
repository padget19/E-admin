<?php

use think\migration\Seeder;

class systemMenuSeeder extends Seeder
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = array(
            0 =>
                array(
                    'id'     => 2,
                    'pid'    => 0,
                    'name'   => '系统管理',
                    'icon'   => '',
                    'url'    => '#',
                    'sort'   => 1,
                    'mark'   => '',
                    'status' => 1,
                ),
            1 =>
                array(
                    'id'     => 3,
                    'pid'    => 4,
                    'name'   => '系统菜单管理',
                    'icon'   => 'el-icon-menu',
                    'url'    => 'admin/menu',
                    'sort'   => 2,
                    'mark'   => '',
                    'status' => 1,
                ),
            2 =>
                array(
                    'id'     => 4,
                    'pid'    => 2,
                    'name'   => '系统配置',
                    'icon'   => 'el-icon-s-tools',
                    'url'    => '',
                    'sort'   => 7,
                    'mark'   => '',
                    'status' => 1,
                ),
            3 =>
                array(
                    'id'     => 5,
                    'pid'    => 12,
                    'name'   => '系统用户管理',
                    'icon'   => 'el-icon-user',
                    'url'    => 'admin/admin',
                    'sort'   => 4,
                    'mark'   => '',
                    'status' => 1,
                ),
            4 =>
                array(
                    'id'     => 7,
                    'pid'    => 12,
                    'name'   => '访问权限管理',
                    'icon'   => 'el-icon-lock',
                    'url'    => 'admin/auth',
                    'sort'   => 6,
                    'mark'   => '',
                    'status' => 1,
                ),
            5 =>
                array(
                    'id'     => 11,
                    'pid'    => 4,
                    'name'   => '系统参数配置',
                    'icon'   => 'el-icon-setting',
                    'url'    => 'admin/system/config',
                    'sort'   => 3,
                    'mark'   => '',
                    'status' => 1,
                ),
            6 =>
                array(
                    'id'     => 12,
                    'pid'    => 2,
                    'name'   => '权限管理',
                    'icon'   => 'el-icon-user-solid',
                    'url'    => '',
                    'sort'   => 5,
                    'mark'   => '',
                    'status' => 1,
                ),
            7 =>
                array(
                    'id'     => 1014,
                    'pid'    => 4,
                    'name'   => '数据库备份',
                    'icon'   => 'fa fa-stack-exchange',
                    'url'    => 'admin/backup',
                    'sort'   => 0,
                    'mark'   => '',
                    'status' => 1,
                ),
            8 =>
                array(
                    'id'     => 1084,
                    'pid'    => 0,
                    'name'   => '总览',
                    'icon'   => '',
                    'url'    => 'admin/index/dashboard',
                    'sort'   => 0,
                    'mark'   => '',
                    'status' => 1,
                ),
        );
        $this->table('system_menu')->insert($data)->save();
    }
}
