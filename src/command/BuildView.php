<?php

namespace Eadmin\command;

use Eadmin\component\basic\Html;
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
    protected $formComponent = [
        'text', 'textarea', 'password', 'mobile',
        'email',  'number', 'money', 'select',
        'radio', 'checkbox', 'switch', 'datetime',
        'date', 'dates', 'year', 'month',
        'time', 'slider', 'rate', 'color', 'file',
        'image', 'images', 'editor', 'cascader',
        'icon', 'selectTable', 'maps', 'tag',
        'checkTag', 'url',
    ];

    protected $tableSpace = "\n\t\t\t\t";

    protected $optionSpace = "\n\t\t\t\t\t";

    protected $modelSpace = "\n\t";

    protected $funcSpace = "\n\n\t";

    protected $commentSpace = "\t";

	protected $modelContentSpace = "\n\t\t";

	protected $endSpace = ";\r\n";

    protected $startSpace = "\t\t\t";

    protected function configure()
    {
        // 指令配置
        $this->setName('make:admin')->setDescription('Create a new BuildView controller class');
        $this->addArgument('name', 1, "The name of the class");
        $this->addOption('model', 1, Option::VALUE_REQUIRED, "The name of the class");
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

        return $stubPath . $name . '.stub';
    }

    /**
     * 获取类名
     * @param string $module 模块名
     * @param string $name 模型名
     * @return string
     */
    protected function getClassNames($module, $name)
    {
        return parent::getClassName($this->getNamespace($module) . '\\' . $name) . (Config::get('controller_suffix') ? ucfirst(Config::get('url_controller_layer')) : '');
    }

    /**
     * @param string $name 类名
     * @param string $type 文件名
	 * @param string $modelContent 模型内容
     * @param string $model 模型名
     * @param string $model_namespace 模型命名空间
     * @param string $grid 表格
     * @param string $detail 详情
     * @param string $form 表单
     * @return false|string|string[]
     */
    protected function buildClasses($name, $type, $modelContent = '', $model = '', $model_namespace = '', $grid = '', $detail = '', $form = '')
    {
        $stub = file_get_contents($this->getStubs($type));
        $namespace = trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
        $class = str_replace($namespace . '\\', '', $name);
        return str_replace([
        	'{%className%}',
			'{%actionSuffix%}',
			'{%namespace%}',
			'{%app_namespace%}',
			'{%model%}',
			'{%model_namespace%}',
			'{%grid%}',
			'{%detail%}', 
			'{%form%}',
			'{%modelContent%}',
		], [
            $class,
            '',
            $namespace,
            App::getNamespace(),
            $model,
            $model_namespace,
            $grid,
            $detail,
            $form,
			$modelContent,
        ], $stub);
    }

    /**
     * 获取表信息
     * @param string $model 模型名称
     * @return string[]
     */
    protected function getTableInfo($model)
    {
        $db = Db::name($model);
        $tableInfo = Db::query('SHOW FULL COLUMNS FROM ' . $db->getTable());
        $fields = $db->getTableFields();
        $grid = '';
        $detail = '';
        $form = '';
        $modelContent = '';
        foreach ($tableInfo as $val) {
            $label = $val['Comment'] ? $val['Comment'] : $val['Field'];
            preg_match('/(?:\{)(.*)(?:\})/i', $val['Comment'], $matchArr);
            preg_match_all("/(?:\[)(.*)(?:\])/i", $val['Comment'], $matchArr2);
            $type = $matchArr[1] ?? [];
            $option = $matchArr2[1][0] ?? [];
            // 正则过滤大括号 和 中括号的内容
            $label = preg_replace('/\[.*?\]/', '', preg_replace('/\{(.*?)\}/', '', $label));
			$action = $this->camelize($val['Field']);
            if (!empty($type) && in_array($type, $this->formComponent)) {
                $origin_type = $type;
                if (in_array($type, ['images'])) {
                    $type = rtrim($type, 's');
                }
                if ($type == 'url') {
                	$type = 'text';
				}
                $form .= $this->startSpace . '$form->' . $type . '(\'' . $val['Field'] . '\',\'' . $label . '\')';
                if (in_array($origin_type, ['select', 'radio', 'checkbox', 'checkTag'])) {
                    if (!empty($option)) {
                        $form .= $this->tableSpace . "->options([";
                        foreach (explode(', ', $option) as $value) {
                            $valueArr = explode(':', $value);
                            if (count($valueArr) == 2) {
                                list($key, $item) = $valueArr;
                                $form .= $this->optionSpace . "{$key} => '{$item}',";
                            }
                        }
						$form .= $this->tableSpace . "])";
                        $form .= $this->endSpace;
                    } else {
                        $form .= $this->tableSpace . '->options([])';
                        $form .= $this->endSpace;
                    }
                    if (in_array($origin_type, ['checkbox'])) {
						if (empty($modelContent)) {
							$modelContent .= $this->commentSpace;
						} else {
							$modelContent .= $this->funcSpace;
						}
                    	$modelContent .= '// '. $label .' - 获取器';
                    	$modelContent .= $this->modelSpace . 'public function get'. $action .'Attr($val){';
                    	$modelContent .= $this->modelContentSpace . 'return array_filter(explode(\',\', $val));';
                    	$modelContent .= $this->modelSpace . '}';
						$modelContent .= $this->funcSpace . '// '. $label .' - 获取器';
						$modelContent .= $this->modelSpace . 'public function set'. $action .'Attr($val){';
						$modelContent .= $this->modelContentSpace . 'if (is_string($val)) return $val;';
						$modelContent .= $this->modelContentSpace . 'return implode(\',\', $val);';
						$modelContent .= $this->modelSpace . '}';
					}
                } elseif ($origin_type == 'switch') {
                    if (!empty($option)) {
                        list($active, $inactive) = explode(', ', $option);
                        $active = explode(':', $active);
                        $inactive = explode(':', $inactive);
                        if (count($active) == 2 && count($inactive) == 2) {
                            list($activeKey, $activeValue) = $active;
                            list($inactiveKey, $inactiveValue) = $inactive;
                            $form .= $this->tableSpace . "->state([";
                            $form .= $this->optionSpace . "[{$activeKey} => '{$activeValue}'],";
                            $form .= $this->optionSpace . "[{$inactiveKey} => '{$inactiveValue}'],";
                            $form .= $this->tableSpace . "])";
                            $form .= $this->endSpace;
                            $grid .= $this->startSpace . '$grid->column(\'' . $val['Field'] . '\',\'' . $label . '\')->switch([';
                            $grid .= $this->optionSpace . "[{$activeKey} => '{$activeValue}'],";
                            $grid .= $this->optionSpace . "[{$inactiveKey} => '{$inactiveValue}'],";
                            $grid .= $this->tableSpace . "])";
                            $grid .= $this->endSpace;
                            $detail .= $this->startSpace . '$detail->field(\'' . $val['Field'] . '\',\'' . $label . '\')->using([';
                            $detail .= $this->optionSpace . "[{$activeKey} => '{$activeValue}'],";
                            $detail .= $this->optionSpace . "[{$inactiveKey} => '{$inactiveValue}'],";
                            $detail .= $this->tableSpace . "])";
                            $detail .= $this->endSpace;
                        } else {
                            $form .= $this->tableSpace . "->state([";
                            $form .= $this->optionSpace . "[1 => '开启'],";
                            $form .= $this->optionSpace . "[0 => '关闭'],";
                            $form .= $this->tableSpace . "])";
                            $form .= $this->endSpace;
                            $grid .= $this->startSpace . '$grid->column(\'' . $val['Field'] . '\',\'' . $label . '\')->switch([';
                            $grid .= $this->optionSpace . "[1 => '开启'],";
                            $grid .= $this->optionSpace . "[0 => '关闭'],";
                            $grid .= $this->tableSpace . "])";
                            $grid .= $this->endSpace;
                            $detail .= $this->startSpace . '$detail->field(\'' . $val['Field'] . '\',\'' . $label . '\')->using([';
                            $detail .= $this->optionSpace . "[1 => '开启'],";
                            $detail .= $this->optionSpace . "[0 => '关闭'],";
                            $detail .= $this->tableSpace . "])";
                            $detail .= $this->endSpace;
                        }
                    } else {
                        $form .= $this->tableSpace . "->state([";
                        $form .= $this->optionSpace . "[1 => '开启'],";
                        $form .= $this->optionSpace . "[0 => '关闭'],";
                        $form .= $this->tableSpace . "])";
                        $form .= $this->endSpace;
                        $grid .= $this->startSpace . '$grid->column(\'' . $val['Field'] . '\',\'' . $label . '\')->switch([';
                        $grid .= $this->optionSpace . "[1 => '开启'],";
                        $grid .= $this->optionSpace . "[0 => '关闭'],";
                        $grid .= $this->tableSpace . "])";
                        $grid .= $this->endSpace;
                        $detail .= $this->startSpace . '$detail->field(\'' . $val['Field'] . '\',\'' . $label . '\')->using([';
                        $detail .= $this->optionSpace . "1 => '开启',";
                        $detail .= $this->optionSpace . "0 => '关闭',";
                        $detail .= $this->tableSpace . "])";
                        $detail .= $this->endSpace;
                    }
                } elseif ($origin_type == 'selectTable') {
                    $form .= $this->tableSpace . '->from($this->index())';
                    $form .= $this->tableSpace . '->options(function ($ids) {';
                    $form .= $this->optionSpace . 'return SystemUser::whereIn(\'id\',$ids)->column(\'nickname\', \'id\');';
                    $form .= $this->tableSpace . '})';
                    $form .= $this->endSpace;
                } elseif (in_array($origin_type, ['image', 'images'])) {
                    if ($origin_type == 'images') {
                        $form .= $this->tableSpace . "->multiple()";
                        $form .= $this->endSpace;
                        $grid .= $this->startSpace . '$grid->column(\'' . $val['Field'] . '\',\'' . $label . '\')->images()';
                        $grid .= $this->endSpace;
                        $detail .= $this->startSpace . '$detail->field(\'' . $val['Field'] . '\',\'' . $label . '\')->images()';
                        $detail .= $this->endSpace;
                        if (empty($modelContent)) {
                        	$modelContent .= $this->commentSpace;
						} else {
                        	$modelContent .= $this->funcSpace;
						}
						$modelContent .=  '// '. $label .' - 获取器';
						$modelContent .= $this->modelSpace . 'public function get'. $action .'Attr($val){';
						$modelContent .= $this->modelContentSpace . 'return array_filter(explode(\',\', $val));';
						$modelContent .= $this->modelSpace . '}';
						$modelContent .= $this->funcSpace . '// '. $label .' - 获取器';
						$modelContent .= $this->modelSpace . 'public function set'. $action .'Attr($val){';
						$modelContent .= $this->modelContentSpace . 'if (is_string($val)) return $val;';
						$modelContent .= $this->modelContentSpace . 'return implode(\',\', $val);';
						$modelContent .= $this->modelSpace . '}';
                    } else {
                        $form .= $this->endSpace;
                        $grid .= $this->startSpace . '$grid->column(\'' . $val['Field'] . '\',\'' . $label . '\')->image()';
                        $grid .= $this->endSpace;
                        $detail .= $this->startSpace . '$detail->field(\'' . $val['Field'] . '\',\'' . $label . '\')->image()';
                        $detail .= $this->endSpace;
                    }
                } elseif ($origin_type == 'url') {
                	$form .= $this->tableSpace . '->urlRule()';
					$form .= $this->endSpace;
					$grid .= $this->startSpace . '$grid->column(\'' . $val['Field'] . '\',\'' . $label . '\')->link()';
					$grid .= $this->endSpace;
					$detail .= $this->startSpace . '$detail->field(\'' . $val['Field'] . '\',\'' . $label . '\')->link()';
					$detail .= $this->endSpace;
				} elseif ($origin_type == 'color') {
                    $form .= $this->endSpace;
                    $grid .= $this->startSpace . '$grid->column(\'' . $val['Field'] . '\',\'' . $label . '\')->display(function ($val) {';
                    $grid .= $this->optionSpace . 'return Html::create()->tag(\'div\')->style([\'width\' => \'20px\', \'height\' => \'20px\', \'background\' => $val]);';
                    $grid .= $this->tableSpace . '})';
                    $grid .= $this->endSpace;
                    $detail .= $this->startSpace . '$detail->field(\'' . $val['Field'] . '\',\'' . $label . '\')->display(function ($val) {';
                    $detail .= $this->optionSpace . 'return Html::create()->tag(\'div\')->style([\'width\' => \'20px\', \'height\' => \'20px\', \'background\' => $val]);';
                    $detail .= $this->tableSpace . '})';
                    $detail .= $this->endSpace;
                } elseif ($origin_type == 'icon') {
                    $form .= $this->endSpace;
                    $grid .= $this->startSpace . '$grid->column(\'' . $val['Field'] . '\',\'' . $label . '\')->display(function ($val) {';
                    $grid .= $this->optionSpace . 'return Html::create()->tag(\'i\')->attr(\'class\', $val);';
                    $grid .= $this->tableSpace . '})';
                    $grid .= $this->endSpace;
                    $detail .= $this->startSpace . '$detail->field(\'' . $val['Field'] . '\',\'' . $label . '\')->display(function ($val) {';
                    $detail .= $this->optionSpace . 'return Html::create()->tag(\'i\')->attr(\'class\', $val);';
                    $detail .= $this->tableSpace . '})';
                    $detail .= $this->endSpace;
                } elseif ($origin_type == 'editor') {
                    $form .= $this->endSpace;
                } elseif ($origin_type == 'textarea') {
                    $form .= $this->endSpace;
                    $grid .= $this->startSpace;
                    $grid .= '$grid->column(\'' . $val['Field'] . '\',\'' . $label . '\')->tip()';
                    $grid .= $this->endSpace;
                    $detail .= $this->startSpace;
                    $detail .= '$detail->field(\'' . $val['Field'] . '\',\'' . $label . '\')->tip()';
                    $detail .= $this->endSpace;
                } else {
                    $form .= $this->endSpace;
                    $grid .= $this->startSpace . '$grid->column(\'' . $val['Field'] . '\',\'' . $label . '\')' . $this->endSpace;
                    $detail .= $this->startSpace . '$detail->field(\'' . $val['Field'] . '\',\'' . $label . '\')' . $this->endSpace;
                }
            } else {
                $form .= $this->startSpace . '$form->text(\'' . $val['Field'] . '\',\'' . $label . '\')' . $this->endSpace;
                $grid .= $this->startSpace . '$grid->column(\'' . $val['Field'] . '\',\'' . $label . '\')' . $this->endSpace;
                $detail .= $this->startSpace . '$detail->field(\'' . $val['Field'] . '\',\'' . $label . '\')' . $this->endSpace;
            }
        }
        if (in_array('create_time', $fields)) {
            $grid .= "\t\t\t" . '$grid->filter(function (Filter $filter){
                $filter->dateRange(\'create_time\',\'创建时间\');
            });' . PHP_EOL;
        }
        return [
            $grid,
            $detail,
            $form,
			$modelContent
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

        if ($input->hasOption('model')) {
            $model = $input->getOption('model');
            $names = explode('/', $model);
            $names = array_filter($names);
            if (isset($names[1])) {
                $model = $names[1];
                $classname_model = $this->getClassNames($names[0], 'model\\' . $names[1]);
            } else {
                $classname_model = $this->getClassNames('', 'model\\' . $model);
            }

            $this->getTableInfo($model);

            $pathname = $this->getPathName($classname_model);
            if (is_file($pathname)) {
                $output->writeln('<error>' . $classname_model . ' already exists!</error>');
            }

            if (!is_dir(dirname($pathname))) {
                mkdir(dirname($pathname), 0755, true);
            }
            list($grid, $detail, $form, $modelContent) = $this->getTableInfo($model);
            if (!is_file($pathname)) {
                file_put_contents($pathname, $this->buildClasses($classname_model, 'model', $modelContent));
            }

        } else {
            $output->writeln('<error>--model not exists</error>');
            return false;
        }

        $name = trim($input->getArgument('name'));
        $names = explode('/', $name);
        $names = array_filter($names);
        if (isset($names[1])) {
            $classname = $this->getClassNames($names[0], 'controller\\' . $names[1]);
            $controller = $names[1];
        } else {
            $classname = $this->getClassNames('admin', 'controller\\' . $name);
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
            if ($model == $controller) {
                $model = '\\' . $classname_model;
                $classname_model = '';
            } else {
                $classname_model = 'use ' . $classname_model . ';';
            }
            file_put_contents($pathname, $this->buildClasses($classname, 'controller', $modelContent, $model, $classname_model, $grid, $detail, $form));
        }
        $output->writeln('<info>created successfully.</info>');
    }

	// 下划线转为驼峰
	public function camelize($str)
	{
		$str = preg_replace_callback('/([-_]+([a-z]{1}))/i',function($matches){
			return strtoupper($matches[2]);
		},$str);
		return ucfirst($str);
	}

}
