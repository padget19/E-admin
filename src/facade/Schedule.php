<?php
namespace Eadmin\facade;
use think\Facade;

/**
 * Class Schedule
 * @package Eadmin\facade
 * @method \Eadmin\Schedule call($name,$closure) static
 */
class Schedule extends Facade
{
    protected static function getFacadeClass()
    {
        return \Eadmin\Schedule::class;
    }
}
