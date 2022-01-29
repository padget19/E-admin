<?php


namespace Eadmin\component\basic;


use Eadmin\Admin;
use Eadmin\component\Component;
use Eadmin\component\form\Field;
use Eadmin\component\form\field\Input;
use Eadmin\component\layout\Content;
use Eadmin\detail\Detail;
use Eadmin\form\Form;
use Eadmin\grid\Grid;
use think\app\Url;
use think\facade\Route;
use think\helper\Str;

/**
 * 对话框
 * Class Dialog
 * @package Eadmin\component\basic
 * @method $this width(string $width) 宽度
 * @method $this fullscreen(bool $value = true) 是否为全屏 Dialog
 * @method $this top(string $top) margin-top 值 15vh
 * @method $this modal(bool $value = true) 是否需要遮罩层
 * @method $this appendToBody(bool $value = true) Dialog 自身是否插入至 body 元素上
 * @method $this lockScroll(bool $value = true) 是否在 Dialog 出现时将 body 滚动锁定
 * @method $this openDelay(int $num) Dialog 打开的延时时间，单位毫秒
 * @method $this closeDelay(int $num) Dialog 关闭的延时时间，单位毫秒
 * @method $this closeOnClickModal(bool $value = true) 是否可以通过点击 modal 关闭 Dialog
 * @method $this closeOnPressEscape(bool $value = true) 是否可以通过按下 ESC 关闭 Dialog
 * @method $this showClose(bool $value = true) 是否显示关闭按钮
 * @method $this center(bool $value = true) 是否对头部和底部采用居中布局
 * @method $this destroyOnClose(bool $value = true) 关闭时销毁 Dialog 中的元素
 * @method $this url(string $value) 异步加载数据url
 * @method $this method(string $value) ajax请求method get / post /put / delete
 * @method $this params(array $value)  异步附加请求数据
 * @method $this gridBatch(bool $value = true)  grid批量操作
 */
class Dialog extends Field
{
    protected $setcion;
    protected $name = 'EadminDialog';
    public function __construct($field = null, $value = '')
    {
        parent::__construct($field, $value);
        $this->bindAttValue('reRender',false,true);
        $this->attr('eadmin_popup', $this->bindAttr('reRender'));
    }
    public static function create($content = null, $field = '')
    {
        $self = new self($field, false);
        $self->destroyOnClose();
        $self->width('35%');
        $self->closeOnPressEscape(false);
        $self->closeOnClickModal(false);
        $self->appendToBody();
        if (!is_null($content)) {
            $self->content($content, 'reference');
        }
        return $self;
    }

    /**
     * 表单异步加载
     * @param mixed $form
     * @return Dialog
     */
    public function form($form)
    {
        $this->url('/eadmin.rest');
        $params = Admin::parseUrlQuery($form);
        $form = Admin::dispatch($form);
        if($form->bind('eadmin_title')){
            $this->title($this->attr('title').$form->bind('eadmin_title'));
        }
        $callMethod = $form->getCallMethod();
        $this->params(array_merge($callMethod,$params));
        //权限
        $this->auth($callMethod['eadmin_class'],$callMethod['eadmin_function']);
        return $this;
    }

    /**
     * @param mixed $content
     * @return Dialog
     */
    public function reference($content)
    {
        return $this->content($content, 'reference');
    }


    /**
     * 标题
     * @param string $content
     * @return Dialog
     */
    public function title($content = null)
    {
        $this->attr('title', $content);
        return $this;
    }
}
