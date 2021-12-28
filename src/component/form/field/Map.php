<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

class Map extends Field
{
    protected $name = 'EadminAmap';

    public function __construct($field = null, string $value = '')
    {
        parent::__construct($field, $value);
        $map = config('admin.map');
        $this->attr('apiKey', $map[$map['default']]['api_key']);
    }


    public function bindFields($fields)
    {
        $this->bindAttr('lng', $fields[0], true);
        $this->bindAttr('lat', $fields[1], true);
    }
}
