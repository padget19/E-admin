<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateSystemQueue extends Migrator
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
        $table = $this->table('system_queue', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci'])->setComment('队列任务');
        $table->addColumn(Column::string('name')->setComment('名称'));
        $table->addColumn(Column::string('queue')->setComment('队列名称'));
        $table->addColumn(Column::text('queue_data')->setNullable()->setComment('队列数据'));
        $table->addColumn(Column::boolean('status')->setDefault(1)->setComment('状态:1等待处理，2正在执行，3已完成，4已失败'));
        $table->addColumn(Column::string('task_time',100)->setNullable()->setComment('耗时毫秒'));
        $table->addColumn(Column::dateTime('plan_time')->setDefault('CURRENT_TIMESTAMP')->setComment('计划时间'));
        $table->addColumn(Column::dateTime('exec_time')->setDefault('CURRENT_TIMESTAMP')->setComment('执行时间'));
        $table->addColumn(Column::dateTime('create_time')->setDefault('CURRENT_TIMESTAMP')->setComment('创建时间'));
        $table->addColumn(Column::dateTime('update_time')->setNullable()->setComment('更新时间'));
        $table->create();
    }
}
