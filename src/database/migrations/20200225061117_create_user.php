<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateUser extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('user', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci'])->setComment('用户表');
        $table->addColumn(Column::string('avatar', 500)->setDefault('')->setComment('头像'));
        $table->addColumn(Column::string('nickname', 50)->setDefault('')->setComment('昵称'));
        $table->addColumn(Column::string('password')->setDefault('')->setComment('密码'));
        $table->addColumn(Column::char('phone', 20)->setDefault('')->setComment('手机号码'));
        $table->addColumn(Column::string('openid', 100)->setDefault('')->setComment('小程序微信openid'));
        $table->addColumn(Column::char('unionid', 100)->setDefault('')->setComment('微信unionid'));
        $table->addColumn(Column::string('country', 100)->setDefault('')->setComment('地区'));
        $table->addColumn(Column::string('province', 100)->setDefault('')->setComment('省份'));
        $table->addColumn(Column::string('city', 100)->setDefault('')->setComment('城市'));
        $table->addColumn(Column::string('area', 100)->setDefault('')->setComment('地区'));
        $table->addColumn(Column::string('address')->setDefault('')->setComment('详情地址'));
        $table->addColumn(Column::decimal('money', 12, 2)->setDefault(0)->setComment('钱包金额'));
		$table->addColumn(Column::dateTime('create_time')->setDefault('CURRENT_TIMESTAMP')->setComment('创建时间'));
		$table->addColumn(Column::dateTime('update_time')->setNullable()->setComment('更新时间'));
        $table->create();
    }
}
