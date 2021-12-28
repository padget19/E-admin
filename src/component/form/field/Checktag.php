<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;
use think\helper\Str;

/**
 * check标签
 * Class Checktag
 * @package Eadmin\component\form\field
 * @method $this multiple(bool $id = true) 是否多选
 * @method $this color(string $value) 背景色
 */
class Checktag extends Field
{
    protected $name = 'EadminCheckTag';
    /**
     * 设置选项数据
     * @param array $data 选项数据
     * @param bool $buttonTheme 是否按钮样式
     * @return $this
     */
    public function options(array $data)
    {
        $options = [];
        foreach ($data as $value => $label) {
            $options[] = [
                'value'    => $value,
                'label'    => $label,
            ];
        }
        $mapField = Str::random(30, 3);
        $this->bindValue($options, 'options', $mapField);
        $this->formItem->form()->except([$mapField]);
        return $this;
    }
}
