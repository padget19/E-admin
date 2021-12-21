<?php

use think\migration\Seeder;

class SystemConfigCateSeeder extends Seeder
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
        $datas = array(
            0 =>
                array(
                    'id' => 1,
                    'name' => '基础配置',
                    'pid' => 0,
                    'status' => 1,
                    'sort' => 0,
                    'mark' => '',
                ),
            1 =>
                array(
                    'id' => 2,
                    'name' => '站点配置',
                    'pid' => 1,
                    'status' => 1,
                    'sort' => 0,
                    'mark' => '',
                ),
            2 =>
                array(
                    'id' => 8,
                    'name' => '数据库备份',
                    'pid' => 1,
                    'status' => 0,
                    'sort' => 1,
                    'mark' => '',
                ),
        );
        $this->table('system_config_cate')->insert($datas)->save();
    }
}
