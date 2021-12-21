<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateSystemConfig extends Migrator
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
        $table = $this->table('system_config', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci'])->setComment('系统-配置');
        $table->addColumn(Column::string('name', 100)->setDefault('')->setComment('配置名'));
        $table->addColumn(Column::mediumText('value')->setNullable()->setComment('配置值'));
        $table->create();
    }
}
