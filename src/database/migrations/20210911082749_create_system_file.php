<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateSystemFile extends Migrator
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
        $table = $this->table('system_file', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci'])->setComment('上传文件');
        $table->addColumn(Column::string('name', 100)->setDefault('')->setComment('文件名称'));
        $table->addColumn(Column::string('real_name', 100)->setDefault('')->setComment('原始文件名'));
        $table->addColumn(Column::integer('cate_id')->setDefault(0)->setComment('分类id'));
        $table->addColumn(Column::string('url')->setDefault('')->setComment('访问url'));
        $table->addColumn(Column::string('path')->setDefault('')->setComment('路径'));
        $table->addColumn(Column::string('file_type',20)->setDefault('')->setComment('文件类型'));
        $table->addColumn(Column::string('ext',10)->setComment('文件后缀'));
        $table->addColumn(Column::bigInteger('file_size')->setComment('文件大小'));
        $table->addColumn(Column::integer('admin_id')->setNullable()->setComment('后台上传人员'));
        $table->addColumn(Column::string('uptype',20)->setDefault('local')->setComment('上传类型'));
        $table->addColumn(Column::boolean('is_delete')->setDefault(0)->setComment('1已删除，0正常'));
        $table->addIndex(['file_type','ext']);
        $table->addTimestamps();
        $table->create();
    }
}
