<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateSystemNotice extends Migrator
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
        $table = $this->table('system_notice',['engine'=>'InnoDB','collation'=>'utf8mb4_unicode_ci'])->setComment('系统通知');
        $table->addColumn(Column::integer('user_id')->setComment('系统用户id'));
        $table->addColumn(Column::string('color')->setDefault('')->setComment('图标和标题标签颜色'));
        $table->addColumn(Column::string('avatar')->setComment('头像或图标'));
        $table->addColumn(Column::tinyInteger('type')->setDefault(1)->setComment('1图标,2头像'));
        $table->addColumn(Column::string('title',20)->setComment('标题'));
        $table->addColumn(Column::string('content')->setComment('内容'));
        $table->addColumn(Column::string('target_url')->setDefault('')->setComment('跳转链接'));
        $table->addColumn(Column::tinyInteger('is_read')->setDefault(0)->setComment('1:已读,0未读'));
		$table->addColumn(Column::dateTime('create_time')->setDefault('CURRENT_TIMESTAMP')->setComment('创建时间'));
		$table->addColumn(Column::dateTime('update_time')->setNullable()->setComment('更新时间'));
        $table->create();
    }
}
