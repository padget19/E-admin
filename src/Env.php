<?php


namespace Eadmin;


use think\helper\Arr;

class Env
{
    protected $data = [];
    public function __construct($env)
    {
        if (is_file($env)) {
            $this->data = parse_ini_file($env, true);
        } else {
            $this->data = parse_ini_string($env, true);
        }
    }

    /**
     * 加载env文件
     * @param $env
     * @return array|false
     */
    public static function load($env)
    {
        $self = new static($env);
        return $self;
    }

    /**
     * 获取值
     * @param string $name 参数名
     * @return array|\ArrayAccess|mixed
     */
    public function get($name,$defualt=null)
    {
        
        $value =  Arr::get($this->data, $name);
        if(is_null($value)){
            return $defualt;
        }
        return $value;
    }
    /**
     * 设置
     */
    public function set($name,$value){
        return Arr::set($this->data, $name,$value);
    }
    /**
     * 将解析的env数组生成env文件内容
     * @param array $env
     * @return string
     */
    public function make(array $env = [])
    {
        if(count($env) == 0){
            $env = $this->data;
        }
        $content = '';
        foreach ($env as $key => $val) {
            if (is_array($val)) {
                $content .= "[{$key}]" . PHP_EOL;
                foreach ($val as $k => $v) {
                    if(is_string($v)){
                        $v = "'{$v}'";
                    }
                    $content .= "{$k} = $v" . PHP_EOL;
                }
            } else {
                if(is_string($val)){
                    $val = "'{$val}'";
                }
                $content .= "{$key} = $val" . PHP_EOL;
            }
        }
        return $content;
    }

    /**
     * 保存env
     * @param string $file 文件路径
     * @return false|int
     */
    public function save($file){
        $content = $this->make();
        return file_put_contents($file,$content);
    }
}
