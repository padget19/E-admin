<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateSystemUser extends Migrator
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
        $table = $this->table('system_user', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci'])->setComment('系统-用户表');
        $table->addColumn(Column::string('username', 50)->setDefault('')->setComment('用户账号'));
        $table->addColumn(Column::string('nickname', 50)->setDefault('')->setComment('用户昵称'));
        $table->addColumn(Column::string('avatar')->setDefault('')->setComment('头像'));
        $table->addColumn(Column::string('password', 255)->setDefault('')->setComment('用户密码'));
        $table->addColumn(Column::string('mail', 32)->setDefault('')->setComment('联系邮箱'));
        $table->addColumn(Column::char('phone', 11)->setDefault('')->setComment('联系手机'));
        $table->addColumn(Column::dateTime('login_at')->setNullable()->setComment('登录时间'));
        $table->addColumn(Column::string('login_ip', 15)->setDefault('')->setComment('登录IP'));
        $table->addColumn(Column::bigInteger('login_num')->setDefault(0)->setComment('登录次数'));
        $table->addColumn(Column::string('desc')->setDefault('')->setComment('备注说明'));
        $table->addColumn(Column::integer('sort')->setDefault(0)->setComment('排序'));
        $table->addColumn(Column::boolean('status')->setDefault(1)->setComment('状态(0:禁用,1:启用)'));
		$table->addColumn(Column::dateTime('create_time')->setDefault('CURRENT_TIMESTAMP')->setComment('创建时间'));
		$table->addColumn(Column::dateTime('update_time')->setNullable()->setComment('更新时间'));
		$table->addColumn(Column::dateTime('delete_time')->setNullable()->setComment('删除时间'));
        $table->create();
    }
}
