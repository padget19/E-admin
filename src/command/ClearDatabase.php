<?php

namespace Eadmin\command;

use app\common\service\BackupData;

use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\facade\Db;
use think\helper\Str;


/**
 * 清空数据库表
 * Class ClearDatabase
 * @package app\common\command
 */
class ClearDatabase extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('database:clear')->setDescription('clear database');
        // 设置参数
        $this->addOption('table', 1, Option::VALUE_OPTIONAL, "deleted table name");

    }

    /**
     * 执行命令
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        $table = $input->getOption('table');
        // 判断有没有指定表
        if (!empty($table)) {
            $table =  Str::snake($table);
        }
        //获取表白名单
        $white_table = config('white_table');

        //获取数据库所有表
        $tables = Db::query('show tables');

        $database = config('database.connections.mysql.database');

        $mark = 'Tables_in_' . $database;
        if (empty($table)) {
            //筛选可以清空的表sql
            $clearTableSqls = [];
            foreach ($tables as $table) {
                if (!in_array($table[$mark], $white_table)) {
                    $clearTableSqls[] = "truncate table {$table[$mark]}";
                }
            }
            $backRes = BackupData::instance()->backup();
            if ($backRes) {
                //清空
                if ($this->output->confirm($this->input, 'Confirm to clear all data? [y]/n')) {
                    foreach ($clearTableSqls as $clearTableSql){
                        Db::execute($clearTableSql);
                    }
                    $output->writeln("<info>tables all clear successfully!</info>");
                }
            }

        } else {
            $tables = array_column($tables, $mark);
            if (in_array($table, $tables)) {
                $clearTableSqls[] = "truncate table {$table}";
                if ($this->output->confirm($this->input, "Confirm to clear {$table} data? [y]/n")) {
                    //清空
                    foreach ($clearTableSqls as $clearTableSql){
                        Db::execute($clearTableSql);
                    }
                    $output->writeln("<info>{$table} delete successfully!</info>");
                }
            } else {
                $output->writeln("<error>{$table} not exist!</error>");
            }
        }
    }
}
