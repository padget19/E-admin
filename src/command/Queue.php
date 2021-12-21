<?php

namespace Eadmin\command;



use Symfony\Component\Filesystem\Filesystem;
use think\console\Command;
use think\console\Input;

use think\console\input\Option;
use think\console\Output;
use think\facade\Db;
use think\facade\Env;
use think\helper\Str;

/**
 * 生成队列任务文件
 * Class Iseed
 * @package app\common\command
 */
class Queue extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('eadmin:job')->setDescription('创建队列任务');
        $this->addArgument('name', 1, "The name of the class");
        $this->addOption('force', 'f', Option::VALUE_NONE, 'Force overwrite file');

    }

    // 获取路径
    protected function getPath()
    {
        return Env::get('root_path') . 'app' . DIRECTORY_SEPARATOR . 'common'.DIRECTORY_SEPARATOR . 'queue';
    }

    /**
     * 执行命令
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        $class = $input->getArgument('name');
        $force = $input->getOption('force');
        $path = $this->getPath();
        if (!file_exists($path)) {
            if ($this->output->confirm($this->input, 'Create queue directory? [y]/n')) {
                $fileSystem  = new Filesystem();
                $fileSystem->mkdir($path, 0755);
            }
        }
        $stub = file_get_contents($this->getStubs('queue'));
        $fileName = Str::studly($class);
        $content = strtr($stub, [
            '$className' => $fileName,
        ]);
        $write = true;
        $res = false;
        $filePath = $path . DIRECTORY_SEPARATOR . $fileName . '.php';
        if (file_exists($filePath)) {
            if (!$force) {
                if(!$this->output->confirm($this->input, "Force overwrite file {$filePath}? [y]/n")){
                    $write = false;
                }
            }
        }
        if($write){
            $res = file_put_contents($filePath, $content);
        }
        if ($res === false) {
            $output->writeln("<error>File write failed</error>");
        } else {
            $output->writeln("<info>Create Seed Success</info>");
        }
    }

    /**
     * 获取文件
     * @param string $name 文件名
     * @return string
     */
    protected function getStubs($name)
    {
        $stubPath = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR;

        return $stubPath . $name . '.stub';
    }

    
}
