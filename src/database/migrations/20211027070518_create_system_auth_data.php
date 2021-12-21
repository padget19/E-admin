<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateSystemAuthData extends Migrator
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
        $table = $this->table('system_auth_data', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci'])->setComment('系统-数据-权限');
        $table->addColumn(Column::tinyInteger('auth_type')->setDefault(1)->setComment('权限类型：1组织(system_auth)，2个人(system_user)'));
        $table->addColumn(Column::integer('auth_id')->setDefault(0)->setComment('权限用户：组织/个人 id'));
        $table->addColumn(Column::tinyInteger('data_type')->setDefault(1)->setComment('数据范围类型：1组织(system_auth)，2个人(system_user)'));
        $table->addColumn(Column::integer('data_id')->setDefault(1)->setComment('数据范围：组织/个人 id'));
        $table->addColumn(Column::dateTime('create_time')->setDefault('CURRENT_TIMESTAMP')->setComment('创建时间'));
        $table->addColumn(Column::dateTime('update_time')->setNullable()->setComment('更新时间'));
        $table->create();
    }
}
