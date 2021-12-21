<?php


namespace Eadmin\command;


use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Db;

/**
 * 整库替换字符,已上线项目谨慎操作,先备份数据库
 * Class ReplaceData
 * @package app\common\command
 */
class ReplaceData extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('replace:data')->setDescription('替换整个数据库字符');
        // 设置参数
        $this->addArgument('search', Argument::REQUIRED, "搜索被替换的字符");
        $this->addArgument('replace', Argument::REQUIRED, "替换的字符");
        $this->addOption('force', 'y', Option::VALUE_NONE, '');
    }
    protected function execute(Input $input, Output $output)
    {
        $search = $input->getArgument('search');
        $replace = $input->getArgument('replace');
        $force = $input->getOption('force');
        $database = config('database.connections.mysql.database');
        $mark = 'Tables_in_' . $database;
        $tables = Db::query('show tables');
        $exec = false;
        if(!$force){
            if ($this->output->confirm($this->input, "Confirm to replace '$search' '$replace' ? [y]/n")) {
                $exec = true;
            }
        }else{
            $exec = true;
        }
        if ($exec) {
            foreach ($tables as $table){
                $table = $table[$mark];
                $fields = Db::name($table)->getFieldsType();
                foreach ($fields as $field=>$type){
                    if($type == 'string'){
                        $sql = "UPDATE {$table} set {$field} = REPLACE({$field},'{$search}','{$replace}')";
                        try {
                            Db::execute($sql);
                        }catch (\Exception $e){

                        }
                    }
                }
            }
            $output->writeln("<info>replace successfully!</info>");
        }
    }
}
