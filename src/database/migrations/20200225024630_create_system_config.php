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
        $table->addColumn(Column::integer('cate_id')->setDefault(0)->setComment('分类id'));
        $table->addColumn(Column::string('name', 100)->setDefault('')->setComment('配置字段'));
        $table->addColumn(Column::string('label', 100)->setDefault('')->setComment('配置名称'));
        $table->addColumn(Column::string('type', 100)->setDefault('')->setComment('类型'));
        $table->addColumn(Column::text('attribute')->setNullable()->setComment('类型属性'));
        $table->addColumn(Column::mediumText('value')->setNullable()->setComment('配置值'));
        $table->addColumn(Column::string('help')->setNullable()->setComment('说明'));
        $table->addColumn(Column::boolean('status')->setDefault(1)->setComment('是否显示'));
		$table->addColumn(Column::string('mark')->setDefault('')->setComment('标记'));
        $table->addColumn(Column::integer('sort')->setDefault(0)->setComment('排序'));
        $table->create();
    }
}
