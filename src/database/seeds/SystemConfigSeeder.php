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
        $datas = array(
            0 =>
                array(
                    'id' => 1,
                    'cate_id' => 2,
                    'label' => '网站名称',
                    'attribute' => '{"multiple":0,"options":"","required":0}',
                    'type' => 'text',
                    'help' => '',
                    'name' => 'web_name',
                    'status' => 1,
                    'value' => '后台模板',
                    'sort' => 0,
                ),
            1 =>
                array(
                    'id' => 2,
                    'cate_id' => 2,
                    'label' => '网站LOGO',
                    'attribute' => '{"multiple":0,"options":"","required":0}',
                    'type' => 'image',
                    'help' => '',
                    'name' => 'web_logo',
                    'status' => 1,
                    'value' => 'https://gw.alipayobjects.com/zos/antfincdn/XAosXuNZyF/BiazfanxmamNRoxxVxka.png',
                    'sort' => 1,
                ),
            2 =>
                array(
                    'id' => 3,
                    'cate_id' => 2,
                    'label' => '网站备案号',
                    'attribute' => '{"multiple":0,"options":"","required":0}',
                    'type' => 'text',
                    'help' => '',
                    'name' => 'web_miitbeian',
                    'status' => 1,
                    'value' => '粤ICP备16006642号-2',
                    'sort' => 2,
                ),
            3 =>
                array(
                    'id' => 4,
                    'cate_id' => 2,
                    'label' => '网站版权信息',
                    'attribute' => '{"multiple":0,"options":"","required":0}',
                    'type' => 'text',
                    'help' => '',
                    'name' => 'web_copyright',
                    'status' => 1,
                    'value' => '©版权所有 2014-2020',
                    'sort' => 3,
                ),

            4 =>
                array(
                    'id' => 65,
                    'cate_id' => 8,
                    'label' => '数据库备份',
                    'attribute' => '{"multiple":0,"options":"","required":0}',
                    'type' => 'switch',
                    'help' => '',
                    'name' => 'databackup_on',
                    'status' => 1,
                    'value' => '1',
                    'sort' => 0,
                ),
            5 =>
                array(
                    'id' => 66,
                    'cate_id' => 8,
                    'label' => '数据库最多保留',
                    'attribute' => '{"multiple":0,"options":"","required":0}',
                    'type' => 'number',
                    'help' => '',
                    'name' => 'database_number',
                    'status' => 1,
                    'value' => '10',
                    'sort' => 2,
                ),
            6 =>
                array(
                    'id' => 67,
                    'cate_id' => 8,
                    'label' => '数据库备份天数间隔',
                    'attribute' => '{"multiple":0,"options":"","required":0}',
                    'type' => 'number',
                    'help' => '',
                    'name' => 'database_day',
                    'status' => 1,
                    'value' => '1',
                    'sort' => 1,
                ),
        );
        $this->table('system_config')->insert($datas)->save();
    }
}
