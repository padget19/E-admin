<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-10-06
 * Time: 12:42
 */

namespace Eadmin\form;

use ArrayAccess;

class Watch implements ArrayAccess
{
    protected $data = [];
    protected $hideField = [];
    protected $showField = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * 显示
     * @param string $field 字段
     */
    public function hide($field)
    {
        $this->hideField[] = $this->getIfField($field);
    }
    /**
     * 隐藏
     * @param string $field 字段
     */
    public function show($field)
    {
        $this->showField[] = $this->getIfField($field);
    }
    protected function getIfField($field){
        $field = str_replace('.','_',$field);
        $field = request()->param('formField').$field.'Show';
        return $field;
    }
    /**
     * 获取字段值
     * @param string $field 字段
     * @return array|mixed
     */
    public function get($field = '')
    {
        if (empty($field)) {
            return $this->data;
        } else {
            return $this->data[$field];
        }
    }

    public function getShowField()
    {
        return $this->showField;
    }

    public function getHideField()
    {
        return $this->hideField;
    }

    /**
     * 设置值
     * @param string $field 字段
     * @param string $value 值
     */
    public function set($field, $value)
    {
        $this->data[$field] = $value;
    }

    public function __get($name)
    {
        return $this->data[$name];
    }

    // ArrayAccess
    public function offsetSet($name, $value)
    {
        $this->set($name, $value);
    }

    public function offsetExists($name): bool
    {
        return $this->__isset($name);
    }

    public function offsetUnset($name)
    {
        $this->__unset($name);
    }

    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * 销毁数据对象的值
     * @access public
     * @param string $name 名称
     * @return void
     */
    public function __unset(string $name): void
    {
        unset($this->data[$name]);
    }

    /**
     * 检测数据对象的值
     * @access public
     * @param string $name 名称
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return !is_null($this->get($name));
    }
}
