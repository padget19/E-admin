<?php
namespace Eadmin\support;
use think\facade\Lang;
use think\helper\Arr;

/**
 * 翻译
 */
class Translator
{
    public function trans(string $name, array $vars = [], string $lang = ''){
        $name = strtolower($name);
        $lang = Lang::getLangSet();
        if(strpos($name,'.')){
            $arr = explode('.',$name);
            $filename = array_shift($arr);
            $value = Lang::get(null,[],$filename.'-'.$lang);
            if(empty($value)){
                $value =  Lang::get(null,[],$lang);
            }else{
                $name = implode('.',$arr);
            }
        }
        return Arr::get($value,$name,$name);
    }
}
