<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

class Display extends Field
{
    protected $name = 'EadminDisplay';
    protected $asClosure = null;
    public function as(\Closure $closure){
        $this->asClosure = $closure;
    }
    public function getClosure(){
        return $this->asClosure;
    }
}
