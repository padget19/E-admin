<?php


namespace Eadmin\component\basic;


use Eadmin\Admin;
use Eadmin\component\Component;
use Eadmin\component\form\Field;
use Eadmin\component\form\field\Input;
use Eadmin\component\layout\Content;
use Eadmin\form\Form;
use think\helper\Str;

/**
 * 抽屉
 * Class Drawer
 * @package Eadmin\component\basic
 * @method $this size(string $width) 宽度
 * @method $this fullscreen(bool $value = true) 是否为全屏 Dialog
 * @method $this top(string $top) margin-top 值 15vh
 * @method $this direction(string $direction) Drawer 打开的方向 rtl / ltr / ttb / btt
 * @method $this modal(bool $value = true) 是否需要遮罩层
 * @method $this closeOnClickModal(bool $value = true) 是否可以通过点击 modal 关闭 Dialog
 * @method $this appendToBody(bool $value = true) Dialog 自身是否插入至 body 元素上
 * @method $this closeOnPressEscape(bool $value = true) 是否可以通过按下 ESC 关闭 Dialog
 * @method $this showClose(bool $value = true) 是否显示关闭按钮
 * @method $this withHeader(bool $value = true) 控制是否显示 header 栏, 默认为 true
 * @method $this destroyOnClose(bool $value = true) 关闭时销毁 Dialog 中的元素
 * @method $this url(string $value) 异步加载数据url
 * @method $this params(array $value)  异步附加请求数据
 * @method $this method(string $value) ajax请求method get / post /put / delete
 */
class Drawer extends Field
{
    protected $setcion;
    protected $name = 'EadminDrawer';
    public function __construct($field = null, $value = '')
    {
        parent::__construct($field, $value);
        $this->bindAttValue('reRender',false,true);
        $this->attr('eadmin_popup', $this->bindAttr('reRender'));
    }

    public static function create($content = null, $field = '')
    {
        $self = new self($field, false);
        $self->closeOnClickModal(false);
        if (!is_null($content)) {
            $self->content($content, 'reference');
        }
        return $self;
    }

    /**
     * 表单异步加载
     * @param Form $form
     * @return Dialog|mixed
     */
    public function form(Form $form)
    {
        $this->url('/eadmin.rest');
        $callMethod = $form->getCallMethod();
        $this->params($callMethod);

        //权限
        $this->auth($callMethod['eadmin_class'],$callMethod['eadmin_function']);
        return $this;
    }

    public function reference($content)
    {
        return $this->content($content, 'reference');
    }

    /**
     * 标题
     * @param string $content
     * @return Drawer
     */
    public function title($content)
    {
        return $this->content(Html::create($content), 'title');
    }
}
