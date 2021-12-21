<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-06-22
 * Time: 21:21
 */

namespace Eadmin\component;


use Eadmin\component\basic\Watch;
use think\Request;

abstract class WatchComponent implements \JsonSerializable
{
    protected $component;
    protected $watch;
    public function __construct()
    {
        $this->component = $this->component();
        $this->component->bindAttr('modelValue',request()->get('field',null),true);
        $this->watch = new Watch();
        $this->watch->attr('params', ['eadmin_class' => static::class, 'eadmin_function' => 'watch','eadmin_app'=>app('http')->getName()]);
        $this->watch->attr('field', $this->component->bindAttr('modelValue'));
        $this->watch->attr('watchComponent',$this->component);
    }

    /**
     * 监听逻辑
     * @param $value 监听值
     * @return mixed
     */
    abstract function watch($value = null);

    /**
     * 监听组件
     * @return Component
     */
    abstract function component(): Component;

    /**
     * 设置渲染内容
     */
    public function content($content){
       $this->watch->content($content);
       return $this->watch;
    }
    public function jsonSerialize()
    {
        return $this->watch;
    }

}
