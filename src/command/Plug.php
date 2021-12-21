<?php


namespace Eadmin\command;


use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class Plug extends Command
{
    protected $package;
    protected $namespace;
    protected $description;
    protected $className;
    protected function configure()
    {
        // 指令配置
        $this->setName('eadmin:plug')->setDescription('生成插件');
        $this->addArgument('name', 1, "插件包名");
        $this->addOption('description', 1, Option::VALUE_REQUIRED,"插件描述");
        $this->addOption('namespace', 2, Option::VALUE_REQUIRED,"命名空间");
    }
    protected function execute(Input $input,Output $output)
    {
        $this->package = $input->getArgument('name');
        $this->namespace = $input->getOption('namespace');
        $this->description = $input->getOption('description');
        $plugDir = $this->app->getRootPath().config('admin.extension.dir','eadmin-plugs');
        if(!is_dir($plugDir)) mkdir($plugDir);
        list($vendor,$plugName) = explode('/',$this->package);
        $this->className = ucfirst($plugName);
        if(is_null($this->namespace)){
           $this->namespace = $plugName;
        }
        //插件分组目录
        $plugDir = $plugDir.DIRECTORY_SEPARATOR.$vendor.DIRECTORY_SEPARATOR;
        if(!is_dir($plugDir)) mkdir($plugDir);
        //插件名目录
        $plugNameDir = $plugDir.$plugName.DIRECTORY_SEPARATOR;
        if(!is_dir($plugNameDir)) mkdir($plugNameDir);
        //src目录
        $plugSrc = $plugNameDir.'src'.DIRECTORY_SEPARATOR;
        if(!is_dir($plugSrc)) mkdir($plugSrc);
        $res =  $this->composerFile($plugNameDir);
        if($res){
            $this->serviceFile($plugSrc);
            $this->versionFile($plugNameDir);
            file_put_contents($plugSrc.'README.md','# E-admin Extension');
            $output->writeln('<info>created successfully.</info>');
        }else{
            $output->error('创建失败 (Creation failed)');
        }
    }
    protected function versionFile($dir){
        $stub =$this->getStubs('version');
        return file_put_contents($dir.'version.php',$stub);
    }
    protected function composerFile($dir){
        $stub =$this->getStubs('composer.json');
        $composerContent = str_replace([
            '{package}',
            '{description}',
            '{namespace}',
            '{className}',
        ],[
            $this->package,
            $this->description,
            str_replace('\\', '\\\\', $this->namespace.'\\'),
            $this->className
        ],$stub);
        $composerFile = $dir.DIRECTORY_SEPARATOR.'composer.json';
        if(is_file($composerFile)){
            $this->output->error('插件命名冲突');
        }else{
            return file_put_contents($composerFile,$composerContent);
        }
    }
    protected function serviceFile($dir){
        $stub = $this->getStubs('service');
        $serviceContent = str_replace([
            '{%namespace%}',
            '{%className%}',
        ],[
            $this->namespace,
            $this->className.'ServiceProvider',
        ],$stub);
        file_put_contents($dir.$this->className.'ServiceProvider.php',$serviceContent);
    }
    protected function getStubs($name)
    {
        $stubPath = __DIR__ . DIRECTORY_SEPARATOR . 'plug' . DIRECTORY_SEPARATOR;
        return  file_get_contents($stubPath . $name.'.stub');
    }
}
