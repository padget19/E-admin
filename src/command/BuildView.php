<?php

namespace Eadmin\command;

use think\console\Command;
use think\console\command\Make;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\facade\App;
use think\facade\Config;
use think\facade\Db;
use think\facade\Log;

class BuildView extends Make
{
    protected function configure()
    {
        // 指令配置
        $this->setName('make:admin')->setDescription('Create a new BuildView controller class');
        $this->addArgument('name', 1, "The name of the class");
        $this->addOption('model', 1, Option::VALUE_REQUIRED,"The name of the class");
        // 设置参数

    }

    protected function getStub()
    {

    }

    /**
     * 获取文件路径
     * @param string $name 文件名
     * @return string
     */
    protected function getStubs($name)
    {
        $stubPath = __DIR__ . DIRECTORY_SEPARATOR . 'buildview' . DIRECTORY_SEPARATOR;

        return $stubPath . $name.'.stub';
    }

    /**
     * 获取类名
     * @param string $module 模块名
     * @param string $name 模型名
     * @return string
     */
    protected function getClassNames($module,$name)
    {
        return parent::getClassName($this->getNamespace( $module) . '\\' . $name) . (Config::get('controller_suffix') ? ucfirst(Config::get('url_controller_layer')) : '');
    }

    /**
     * @param string $name 类名
     * @param string $type  文件名
     * @param string $model 模型名
     * @param string $model_namespace 模型命名空间
     * @param string $grid 表格
     * @param string $detail 详情
     * @param string $form 表单
     * @return false|string|string[]
     */
    protected function buildClasses($name,$type,$model='',$model_namespace='',$grid='',$detail='',$form='')
    {
        $stub = file_get_contents($this->getStubs($type));

        $namespace = trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
        $class = str_replace($namespace . '\\', '', $name);
        return str_replace(['{%className%}', '{%actionSuffix%}', '{%namespace%}', '{%app_namespace%}','{%model%}','{%model_namespace%}','{%grid%}','{%detail%}','{%form%}'], [
            $class,
            '',
            $namespace,
            App::getNamespace(),
            $model,
            $model_namespace,
            $grid,
            $detail,
            $form
        ], $stub);
    }

    /**
     * 获取表信息
     * @param string $model 模型名称
     * @return string[]
     */
    protected function getTableInfo($model){
        $db = Db::name($model);
        $tableInfo= Db::query('SHOW FULL COLUMNS FROM '.$db->getTable());
        $fields = $db->getTableFields();
        $grid = '';
        $detail = '';
        $form = '';
        $imgType = ['cover', 'image', 'images', 'avatar', 'avatars', 'img', 'imgs', 'carousel', 'picture', 'pictures'];
        $formFilter = ['status', 'create_at', 'update_at', 'create_time', 'update_time', 'id', 'sort'];
        $gridFilter = ['id', 'sort'];
        foreach ($tableInfo as $val){
            $label = $val['Comment']?$val['Comment']:$val['Field'];
            if (!in_array($val['Field'], $gridFilter)) {
                if (in_array($val['Field'], $imgType)) {
                    $grid .= "\t\t\t" . '$grid->column(\'' . $val['Field'] . '\',\'' . $label . '\')->image();' . PHP_EOL;
                } elseif(strpos($val['Field'], 'status') !== false) {
                    $grid .= "\t\t\t" . '$grid->column(\'' . $val['Field'] . '\',\'' . $label . '\')->switch();' . PHP_EOL;
                } else {
                    $grid .= "\t\t\t" . '$grid->column(\'' . $val['Field'] . '\',\'' . $label . '\');' . PHP_EOL;
                }
            }
            $detail .= "\t\t\t".'$detail->field(\''.$val['Field'].'\',\''.$label.'\');'.PHP_EOL;
            if (!in_array($val['Field'], $formFilter)) {
                if(strstr($val['Type'],'varchar')){
                    if (in_array($val['Field'], $imgType)) {
                        if (strrpos($val['Field'], 's') !== false) {
                            $form .= "\t\t\t".'$form->image(\''.$val['Field'].'\',\''.$label.'\')->multiple();'.PHP_EOL;
                        } else {
                            $form .= "\t\t\t".'$form->image(\''.$val['Field'].'\',\''.$label.'\');'.PHP_EOL;
                        }
                    } else {
                        $form .= "\t\t\t".'$form->text(\''.$val['Field'].'\',\''.$label.'\');'.PHP_EOL;
                    }
                }elseif(strstr($val['Type'],'char')){
                    $form .= "\t\t\t".'$form->text(\''.$val['Field'].'\',\''.$label.'\');'.PHP_EOL;
                }elseif (strstr($val['Type'],'timestamp')){
                    $form .= "\t\t\t".'$form->datetime(\''.$val['Field'].'\',\''.$label.'\');'.PHP_EOL;
                }elseif (strstr($val['Type'],'datetime')){
                    $form .= "\t\t\t".'$form->datetime(\''.$val['Field'].'\',\''.$label.'\');'.PHP_EOL;
                }elseif (strstr($val['Type'],'date')){
                    $form .= "\t\t\t".'$form->date(\''.$val['Field'].'\',\''.$label.'\');'.PHP_EOL;
                }elseif (strstr($val['Type'],'time')){
                    $form .= "\t\t\t".'$form->time(\''.$val['Field'].'\',\''.$label.'\');'.PHP_EOL;
                }elseif (strstr($val['Type'],'tinyint')){
                    $form .= "\t\t\t".'$form->switch(\''.$val['Field'].'\',\''.$label.'\');'.PHP_EOL;
                }elseif (strstr($val['Type'],'int') || strstr($val['Type'],'decimal')){
                    $form .= "\t\t\t".'$form->number(\''.$val['Field'].'\',\''.$label.'\');'.PHP_EOL;
                }elseif (strstr($val['Type'],'text')){
                    $form .= "\t\t\t".'$form->editor(\''.$val['Field'].'\',\''.$label.'\');'.PHP_EOL;
                }else{
                    $form .= "\t\t\t".'$form->text(\''.$val['Field'].'\',\''.$label.'\');'.PHP_EOL;
                }
            }
        }
        if(in_array('create_time',$fields)){
            $grid .= "\t\t\t".'$grid->filter(function (Filter $filter){
                $filter->dateRange(\'create_time\',\'创建时间\');
            });'.PHP_EOL;
        }
        return [
            $grid,
            $detail,
            $form,
        ];
    }

    /**
     * 执行命令
     * @param Input $input
     * @param Output $output
     * @return bool
     */
    protected function execute(Input $input, Output $output)
    {
        
        if($input->hasOption('model')){
            $model = $input->getOption('model');
            $names = explode('/',$model);
            $names = array_filter($names);
            if(isset($names[1])){
                $model = $names[1];
                $classname_model = $this->getClassNames($names[0],'model\\'.$names[1]);
            }else{
                $classname_model = $this->getClassNames('','model\\'.$model);
            }

            $this->getTableInfo($model);

            $pathname = $this->getPathName($classname_model);
            if (is_file($pathname)) {
                $output->writeln('<error>' . $classname_model . ' already exists!</error>');
            }

            if (!is_dir(dirname($pathname))) {
                mkdir(dirname($pathname), 0755, true);
            }
            list($grid,$detail,$form) = $this->getTableInfo($model);
            if (!is_file($pathname)) {
                file_put_contents($pathname, $this->buildClasses($classname_model,'model'));
            }

        }else{
            $output->writeln('<error>--model not exists</error>');
            return false;
        }

        $name = trim($input->getArgument('name'));
        $names = explode('/',$name);
        $names = array_filter($names);
        if(isset($names[1])){
            $classname = $this->getClassNames($names[0],'controller\\'.$names[1]);
            $controller = $names[1];
        }else{
            $classname = $this->getClassNames('admin','controller\\'.$name);
            $controller = $name;
        }
        $pathname = $this->getPathName($classname);
        if (is_file($pathname)) {
            $output->writeln('<error>' . $classname . ' already exists!</error>');
            return false;
        }
        if (!is_dir(dirname($pathname))) {
            mkdir(dirname($pathname), 0755, true);
        }
        if (!is_file($pathname)) {
            if($model == $controller){
                $model = '\\'.$classname_model;
                $classname_model = '';
            }else{
                $classname_model = 'use '.$classname_model.';';
            }
            file_put_contents($pathname, $this->buildClasses($classname,'controller',$model,$classname_model,$grid,$detail,$form));
        }
        $output->writeln('<info>created successfully.</info>');
    }
}
