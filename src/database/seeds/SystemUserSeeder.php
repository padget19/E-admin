<?php

use think\migration\Seeder;

class SystemUserSeeder extends Seeder
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
        $data = [
            'id'       => 1,
            'username' => 'admin',
            'avatar' => 'https://gw.alipayobjects.com/zos/antfincdn/XAosXuNZyF/BiazfanxmamNRoxxVxka.png',
            'nickname' => 'admin',
            'password' => password_hash('admin', PASSWORD_DEFAULT),
        ];
        $this->table('system_user')->insert($data)->save();
    }
}
