<?php


namespace Eadmin\form\traits;


use Eadmin\component\basic\Html;
use Eadmin\component\form\field\Cascader;
use Eadmin\component\form\field\CheckboxGroup;
use Eadmin\component\form\field\Checktag;
use Eadmin\component\form\field\Color;
use Eadmin\component\form\field\DatePicker;
use Eadmin\component\form\field\Display;
use Eadmin\component\form\field\DynamicTag;
use Eadmin\component\form\field\Editor;
use Eadmin\component\form\field\Email;
use Eadmin\component\form\field\Icon;
use Eadmin\component\form\field\Input;
use Eadmin\component\form\field\Map;
use Eadmin\component\form\field\Mobile;
use Eadmin\component\form\field\Money;
use Eadmin\component\form\field\Number;
use Eadmin\component\form\field\RadioGroup;
use Eadmin\component\form\field\Rate;
use Eadmin\component\form\field\Select;
use Eadmin\component\form\field\SelectTable;
use Eadmin\component\form\field\Slider;
use Eadmin\component\form\field\Spec;
use Eadmin\component\form\field\Switchs;
use Eadmin\component\form\field\Table;
use Eadmin\component\form\field\TimePicker;
use Eadmin\component\form\field\Transfer;
use Eadmin\component\form\field\Tree;
use Eadmin\component\form\field\Upload;

trait ComponentForm
{
    protected static $component = [
        'text' => Input::class,
        'hidden' => Input::class,
        'textarea' => Input::class,
        'password' => Input::class,
        'mobile' => Mobile::class,
        'email' => Email::class,
        'number' => Number::class,
        'select' => Select::class,
        'radio' => RadioGroup::class,
        'checkbox' => CheckboxGroup::class,
        'switch' => Switchs::class,
        'datetime' => DatePicker::class,
        'datetimeRange' => DatePicker::class,
        'dateRange' => DatePicker::class,
        'date' => DatePicker::class,
        'dates' => DatePicker::class,
        'year' => DatePicker::class,
        'month' => DatePicker::class,
        'timeRange' => TimePicker::class,
        'time' => TimePicker::class,
        'slider' => Slider::class,
        'color' => Color::class,
        'rate' => Rate::class,
        'file' => Upload::class,
        'image' => Upload::class,
        'editor' => Editor::class,
        'tree' => Tree::class,
        'cascader' => Cascader::class,
        'transfer' => Transfer::class,
        'icon' => Icon::class,
        'selectTable' => SelectTable::class,
        'maps' => Map::class,
        'tag' => DynamicTag::class,
        'spec' => Spec::class,
        'display' => Display::class,
        'money' => Money::class,
        'checkTag' => Checktag::class,
    ];
}
