<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;
use Eadmin\component\form\FormItem;
use Eadmin\traits\ApiJson;
use think\facade\Request;
use think\helper\Str;

/**
 * 选择器
 * Class Select
 * @link https://element-plus.gitee.io/#/zh-CN/component/input-number
 * @method $this multiple(bool $id = true) 是否多选
 * @method $this size(string $size) 输入框尺寸    medium / small / mini
 * @method $this clearable(bool $id = true) 是否可以清空选项
 * @method $this collapseTags(bool $id = true) 多选时是否将选中值按文字的形式展示
 * @method $this multipleLimit(int $num) 多选时用户最多可以选择的项目数，为 0 则不限制
 * @method $this autocomplete(string $complete) select input 的 autocomplete 属性
 * @method $this placeholder(string $text) 占位符
 * @method $this filterable(bool $id = true) 是否可搜索
 * @method $this allowCreate(bool $id = true) 是否允许用户创建新条目，需配合 filterable 使用
 * @method $this remote(bool $id = true) 是否为远程搜索
 * @method $this loading(bool $id = true) 是否正在从远程获取数据
 * @method $this loadingText(string $text) 远程加载时显示的文字
 * @method $this noMatchText(string $text) 搜索条件无匹配时显示的文字，也可以使用 #empty 设置
 * @method $this noDataText(string $text) 选项为空时显示的文字，也可以使用 #empty 设置
 * @method $this popperClass(string $text) Select 下拉框的类名
 * @method $this reserveKeyword(bool $id = true) 多选且可搜索时，是否在选中一个选项后保留当前的搜索关键词
 * @method $this defaultFirstOption(bool $id = true) 在输入框按下回车，选择第一个匹配项。需配合 filterable 或 remote 使用
 * @method $this popperAppendToBody(bool $id = true) 是否将弹出框插入至 body 元素。在弹出框的定位出现问题时，可将该属性设置为 false
 * @method $this automaticDropdown(bool $id = true) 对于不可搜索的 Select，是否在输入框获得焦点后自动弹出选项菜单
 * @method $this clearIcon(string $icon) 自定义清空图标的类名
 * @package Eadmin\component\form\field
 */
class Select extends Field
{
    use ApiJson;

    protected $name = 'EadminSelect';
    //禁用数据
    protected $disabledData = [];

    protected $selectOption = null;

    protected $optionBindField = null;

    public function __construct($field = null, $id = '')
    {
        parent::__construct($field, $id);
        $this->clearable();
        $this->filterable();
        $this->selectOption = SelectOption::create();
        $this->optionBindField = Str::random(30, 3);
    }

    /**
     * 禁用选项数据
     * @param array $data 禁用数据
     */
    public function disabledData(array $data)
    {
        $this->disabledData = $data;
        return $this;
    }

    /**
     * 设置分组选项数据
     * @param array  $data
     * @param string $name 分组字段名
	 * @param string $label label字段
	 * @param string $id id字段
	 * @return $this
     */
    public function groupOptions(array $data, $name = 'options', $label = 'label', $id = 'id')
    {
        /* 格式
         $data = [
            [
                'label' => '第一个分组',
                'id' => 2,
                'options' => [
                    [
                        'label' => '第一个标签',
                        'id' => 1
                    ]
                ]
            ],
            [
                'label' => '第二个分组',
                'id' => 2,
                'options' => [
                    [
                        'label' => '第二个标签',
                        'id' => 2
                    ]
                ]
            ]
         ];
        */
        $bindOptions = [];
        foreach ($data as $key => $option) {
            $options = [];
            if (in_array($option[$id], $this->disabledData)) {
                $disabled = true;
            } else {
                $disabled = false;
            }
            foreach ($option[$name] as $item) {
                if (in_array($item[$id], $this->disabledData)) {
                    $disabled = true;
                } else {
                    $disabled = false;
                }
                $options[] = [
                    'id' => $item[$id],
                    'label' => $item[$label],
                    'disabled' => $disabled,
                ];
            }
            $bindOptions = array_merge($bindOptions,$options);
            $selectOption = SelectOption::create();
            $selectOption->map($options)
                ->mapAttr('label', 'label')
                ->mapAttr('key', 'id')
                ->mapAttr('value', 'id')
                ->mapAttr('disabled', 'disabled');
            $selectGroup = OptionGroup::create()
                ->attr('label', $option[$label])
                ->attr('disabled', $disabled)
                ->content($selectOption);
            $this->content($selectGroup);
        }
        $this->bindValue($bindOptions, 'options', $this->optionBindField);
        if ($this->formItem) {
			$this->formItem->form()->except([$this->optionBindField]);
		}
        return $this;
    }

    /**
     * 联动select
     * @param \Closure $select
     * @param $closure
     * @return Select
     */
    public function load(\Closure $select,$closure)
    {

        $formItems = $this->formItem->form()->collectFields($select);
        foreach ($formItems as $formItem) {
            $select = $formItem->content['default'][0];
            if(is_null($this->bindAttr('loadOptionField'))){
                $this->bindAttr('loadOptionField', $select->optionBindField, true);
                $this->bindAttr('loadField', $select->bindAttr('modelValue'), true);
            }
            $this->formItem->form()->push($formItem);
        }
        if (Request::has('eadminSelectLoad') && Request::get('eadmin_field') == $select->bindAttr('modelValue')) {
            $datas = call_user_func($closure, Request::get('eadmin_id'));
            $options = [];
            foreach ($datas as $key => $value) {
                $options[] = [
                    'id' => $key,
                    'label' => $value
                ];
            }
            $this->successCode($options);
        }
        $this->params(['eadmin_field' => $select->bindAttr('modelValue')] + $this->formItem->form()->getCallMethod());
        return $this;
    }
    /**
     * 设置选项数据
     * @param array $data 选项数据
     * @param array $disable 禁用选项数据
     * @return Select
     */
    public function options(array $data,array $disable = [])
    {
        $this->disabledData = $disable;
        $options = [];
        foreach ($data as $id => $label) {
            if (in_array($id, $this->disabledData)) {
                $disabled = true;
            } else {
                $disabled = false;
            }
            $options[] = [
                'id' => $id,
                'label' => $label,
                'disabled' => $disabled,
            ];
        }

        $this->bindValue($options, 'options', $this->optionBindField);
        $mapField = $this->optionBindField;
        if($this->formItem){
            $this->formItem->form()->except([$this->optionBindField]);
            if (empty($this->formItem->form()->manyRelation())) {
                $mapField = $this->formItem->form()->bindAttr('model') . '.' . $this->optionBindField;
            }
        }
        $this->selectOption->map($options, $mapField)
            ->mapAttr('label', 'label')
            ->mapAttr('key', 'id')
            ->mapAttr('value', 'id')
            ->mapAttr('disabled', 'disabled');
        return $this->content($this->selectOption);
    }

}
