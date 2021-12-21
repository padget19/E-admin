<?php


namespace Eadmin\component\form\field;


use Eadmin\component\Component;

class OptionGroup extends Component
{
    protected $name = 'ElOptionGroup';

    public static function create()
    {
        return new static();
    }
}
