<?php

namespace Eadmin\command;


use app\model\McMedium;
use Eadmin\service\BackupData;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use Eadmin\facade\Schedule;
use Symfony\Component\Filesystem\Filesystem;
use think\facade\Event;

/**
 * 定时任务
 * Class Crontab
 * @package app\common\command
 */
class Crontab extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('crontab')->setDescription('Used for scheduling timed tasks');
        $this->addOption('daemon', 'd', Option::VALUE_NONE, 'run');
    }
    protected function execute(Input $input, Output $output)
    {
        if($input->hasOption('daemon')){
            $phpLibry = (new PhpExecutableFinder)->find(false);
            $cmd = [
                $phpLibry,
                'think',
                'crontab',
            ];

            $process = new Process($cmd,app()->getRootPath());
            $process->run();
            while (true){
                $process->run(function ($type, $line) use($output) {
                    $output->write($line);
                });
            }
        }else{
            Event::trigger(\Eadmin\Schedule::class);
        }
    }
}
