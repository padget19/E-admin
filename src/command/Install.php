<?php


namespace Eadmin\command;


use Symfony\Component\Finder\Finder;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\facade\Console;
use think\facade\Db;

class Install extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('eadmin:install')->setDescription('安装 eadmin insall');
        $this->addOption('force', 'f', Option::VALUE_NONE, 'Force overwrite file');
    }

    /**
     * 执行命令
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        $force = $input->getOption('force');
        $params = [];
        if($force){
            //获取数据库所有表
            $tables = Db::query('show tables');
            $database = config('database.connections.mysql.database');
            $mark = 'Tables_in_' . $database;
            $clearTableSqls = [];
            foreach ($tables as $table) {
                Db::execute("DROP TABLE {$table[$mark]}");
            }
            $params[] = '-f';
        }
        $path = dirname(__DIR__);
        Console::call('migrate:eadmin',['cmd'=>'run','path'=>$path.'/database/migrations']);
        Console::call('seed:eadmin',['path'=> $path.'/database/seeds']);
        Console::call('eadmin:publish',$params);
        $configPath = $path.'/assets/config';
        $finder = new Finder();
        foreach ($finder->in($configPath) as $file) {
            $path = $file->getRealPath();
            copy($path,app()->getConfigPath().$file->getFilename());
        }
        $output->writeln("<info>install success</info>");
    }
}
