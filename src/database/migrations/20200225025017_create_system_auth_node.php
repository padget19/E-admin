<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateSystemAuthNode extends Migrator
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
        $table = $this->table('system_auth_node', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci'])->setComment('系统-权限-授权');
        $table->addColumn(Column::string('node_id', 32)->setDefault('')->setComment('节点id'));
        $table->addColumn(Column::integer('auth_id')->setDefault(0)->setComment('角色'));
        $table->addColumn(Column::string('class', 50)->setDefault('')->setComment('类名'));
        $table->addColumn(Column::string('action', 50)->setDefault('')->setComment('方法'));
        $table->addColumn(Column::string('method', 50)->setDefault('')->setComment('请求方法'));
		$table->addColumn(Column::dateTime('create_time')->setDefault('CURRENT_TIMESTAMP')->setComment('创建时间'));
		$table->addColumn(Column::dateTime('update_time')->setNullable()->setComment('更新时间'));
        $table->create();
    }
}
