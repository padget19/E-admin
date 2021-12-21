<?php


namespace Eadmin\traits;


trait CallProvide
{
    protected $callMethod = [];
    protected $callClass;
    protected $callFunction;
    protected $callParams = [];
    protected $execClosure = null;
    protected $exec = true;
    public function parseCallMethod($reset = false,$offset = 2)
    {
        if (empty($this->callMethod) || $reset) {
            $this->callParams = [];
            $backtrace          = debug_backtrace(1, 3);
            $backtrace          = array_slice($backtrace, $offset);
            $backtrace          = $backtrace[0];
            $this->callClass    = $backtrace['class'];
            $this->callFunction = $backtrace['function'];
            $class              = new \ReflectionClass($this->callClass);
            try {
                $params = $class->getMethod($this->callFunction)->getParameters();
                foreach ($params as $key => $param) {
                    $name                    = $param->getName();
                    $this->callParams[$name] = isset($backtrace['args'][$key]) ? $backtrace['args'][$key] : $param->getDefaultValue();
                }
            }catch (\Exception $exception){

            }
            $this->callMethod = [
                'eadmin_class'    => $this->callClass,
                'eadmin_function' => $this->callFunction,
                'eadmin_app' => app('http')->getName(),
            ];
            $this->callMethod = array_merge($this->callMethod,$this->callParams);
        }
    }
    public function getCallMethod(){
        $this->exec = false;
        return $this->callMethod;
    }
    public function setExec(\Closure $closure){
        $this->execClosure = $closure;
    }
    public function exec(){

        if(!is_null($this->execClosure) && $this->exec){
            call_user_func($this->execClosure,$this);
        }
        return $this;
    }
    public function renderable()
    {

        $method = $this->callFunction;
        return call_user_func_array([app($this->callClass),$method],array_values($this->callParams));
    }
}
