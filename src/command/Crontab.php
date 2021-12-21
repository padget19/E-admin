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
    protected $exec_time = null;
    protected function configure()
    {
        // 指令配置
        $this->setName('crontab')->setDescription('Used for scheduling timed tasks');
        $this->addOption('key', 'k', Option::VALUE_OPTIONAL, 'key');
        $this->addOption('daemon', 'd', Option::VALUE_NONE, 'run');
    }
    protected function execute(Input $input, Output $output)
    {
        if($input->hasOption('daemon')){
            $phpLibry = (new PhpExecutableFinder)->find(false);
            while (true) {
                foreach (\Eadmin\Schedule::$crontab as $crontab){
                    $cmd = [
                        $phpLibry,
                        'think',
                        'crontab',
                        '--key='.$crontab['key']
                    ];
                    if($crontab['schedule']->isMinuteTask()){
                        if($this->exec_time == date('i')){
                           
                           continue;
                        }
                        $this->exec_time = date('i');
                    }
                    $process = new Process($cmd,app()->getRootPath());
                    $process->start();
                }
                sleep(1);
            }
        }else{
            $key = $input->getOption('key');
            Event::trigger(\Eadmin\Schedule::class, $key);
        }
    }
}
