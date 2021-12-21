<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateSystemMenu extends Migrator
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
        $table = $this->table('system_menu', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci'])->setComment('系统-菜单表');
        $table->addColumn(Column::bigInteger('pid')->setDefault(0)->setComment('父ID'));
        $table->addColumn(Column::string('name', 100)->setDefault('')->setComment('名称'));
        $table->addColumn(Column::string('icon', 100)->setDefault('')->setComment('菜单图标'));
        $table->addColumn(Column::string('url', 400)->setDefault('')->setComment('链接'));
        $table->addColumn(Column::string('mark')->setDefault('')->setComment('标记'));
        $table->addColumn(Column::integer('sort')->setDefault(0)->setComment('菜单排序'));
        $table->addColumn(Column::tinyInteger('status')->setDefault(1)->setComment('状态(0:禁用,1:启用)'));
        $table->addColumn(Column::tinyInteger('admin_visible')->setDefault(1)->setComment('超级管理员状态(0:隐藏,1:显示)'));
		$table->addColumn(Column::dateTime('create_time')->setDefault('CURRENT_TIMESTAMP')->setComment('创建时间'));
		$table->addColumn(Column::dateTime('update_time')->setNullable()->setComment('更新时间'));
        $table->create();
    }
}
