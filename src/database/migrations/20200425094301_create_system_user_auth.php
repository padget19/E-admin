<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateSystemUserAuth extends Migrator
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
        $table = $this->table('system_user_auth', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci'])->setComment('系统用户授权角色表');
        $table->addColumn(Column::integer('user_id')->setComment('系统用户id'));
        $table->addColumn(Column::integer('auth_id')->setComment('权限角色id'));
        $table->create();
    }
}
