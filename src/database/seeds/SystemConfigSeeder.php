<?php

use think\migration\Seeder;

class SystemConfigSeeder extends Seeder
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
                    'id'    => 1,
                    'name'  => 'web_name',
                    'value' => 'E-admin',
                ),
            1 =>
                array(
                    'id'    => 2,
                    'name'  => 'web_logo',
                    'value' => 'https://gw.alipayobjects.com/zos/antfincdn/XAosXuNZyF/BiazfanxmamNRoxxVxka.png',
                ),
            2 =>
                array(
                    'id'    => 3,
                    'name'  => 'web_miitbeian',
                    'value' => '粤ICP备16006642号-2',
                ),
            3 =>
                array(
                    'id'    => 4,
                    'name'  => 'web_copyright',
                    'value' => '©版权所有 2014-2020',
                ),
            4 =>
                array(
                    'id'    => 5,
                    'name'  => 'databackup_on',
                    'value' => '1',
                ),
            5 =>
                array(
                    'id'    => 6,
                    'name'  => 'database_number',
                    'value' => '10',
                ),
            6 =>
                array(
                    'id'    => 7,
                    'name'  => 'database_day',
                    'value' => '1',
                ),
        );
        $this->table('system_config')->insert($data)->save();
    }
}
