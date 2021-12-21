<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

/**
 * 穿梭框
 * Class ColorPicker
 * @link https://element-plus.gitee.io/#/zh-CN/component/transfer
 * @method $this filterable(bool $value = true) 是否可搜索
 * @method $this filterPlaceholder(string $text) 搜索框占位符
 * @method $this targetOrder(string $order) 右侧列表元素的排序策略：若为 original，则保持与数据源相同的顺序；若为 push，则新加入的元素排在最后；若为 unshift，则新加入的元素排在最前
 * original / push / unshift
 * @method $this titles(array $title) 自定义列表标题
 * @method $this buttonTexts(array $texts) 自定义按钮文案
 * @method $this leftDefaultChecked(array $texts) 初始状态下左侧列表的已勾选项的 key 数组
 * @method $this rightDefaultChecked(array $texts) 初始状态下右侧列表的已勾选项的 key 数组
 * @package Eadmin\component\form\field
 */
class Transfer extends Field
{
    protected $name = 'ElTransfer';

    public function __construct($field = null, string $value = '')
    {
        parent::__construct($field, $value);
        $this->filterable();
        $this->default = [];
        $this->attr('props', ['key' => 'id', 'label' => 'label', 'disabled' => 'disabled']);
    }

    /**
     * 据源 array[{ id, label, disabled }]
     * @param $data
     * @return $this
     */
    public function options($data)
    {
        $this->attr('data', $data);
        return $this;
    }
}