<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-01-01
 * Time: 15:15
 */

namespace Eadmin\component\form\field;


use Eadmin\component\Component;

class SelectOption extends Component
{
    protected $name = 'ElOption';

    public static function create()
    {
        return new static();
    }
}
