<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-12-27
 * Time: 20:39
 */

namespace Eadmin\component\basic;


use Eadmin\Admin;
use Eadmin\component\Component;
use Eadmin\component\form\field\Upload;
use think\app\Url;
use think\facade\Route;

/**
 * Class Button
 * @package Eadmin\component\basic
 * @method $this disabled(bool $value = true) 是否禁用状态
 * @method $this loading(bool $value = true) 是否加载中状态
 * @method $this circle(bool $value = true) 是否圆形按钮
 * @method $this round(bool $value = true) 是否圆角按钮
 * @method $this plain(bool $value = true) 是否朴素按钮
 * @method $this type(string $value) 类型 primary / success / warning / danger / info / text
 * @method $this nativeType(string $value) 原生type属性 button / submit / reset
 * @method $this size(string $value) 尺寸 medium / small / mini
 * @method $this icon(string $value) 图标
 * @method $this url(string $value) ajax请求url
 * @method $this method(string $value) ajax请求method get / post /put / delete
 * @method $this params(array $value) 提交ajax参数
 */
class Button extends Component
{
    use Common;

    protected $name = 'EadminButton';

    public function __construct($content)
    {
        parent::__construct();
        $this->sizeSmall();
        $this->event('gridRefresh', []);
        $this->content($content);
    }

    /**
     * 创建一个按钮
     * @param string $content 按钮内容
     * @return Button
     */
    public static function create(string $content)
    {
        return new self($content);
    }

    /**
     * 模态对话框
     * @return Dialog
     */
    public function dialog()
    {
        $dialog = Dialog::create($this)->title($this->content['default'][0]);
        return $dialog;
    }


    /**
     * 抽屉
     * @return Drawer
     */
    public function drawer()
    {
        $drawer = Drawer::create($this);
        return $drawer;
    }

    /**
     * 确认消息框
     * @param string $message 确认内容
     * @param string $url 请求url 空不请求
     * @param array $params 请求参数
     * @return Confirm
     */
    public function confirm(string $message, string $url = '', array $params = [])
    {

        $confirm = Confirm::create($this)
            ->message($message)->url($url)->params($params);
        $dispatch = Admin::getDispatch($url);
        if($dispatch){
            list($eadmin_class, $eadmin_function)  = Admin::getDispatchCall($dispatch);
            $confirm->auth($eadmin_class,$eadmin_function);
        }
        return $confirm;
    }

    /**
     * 保存数据
     * @param array $data 数据内容
     * @param mixed $url 跳转地址
     * @param string $confirm 消息文案
     * @return $this|Confirm
     */
    public function save(array $data, $url, $confirm = '')
    {
        if($url instanceof Url){
            $url = (string)$url;
        }
        $dispatch = Admin::getDispatch($url);
        if($dispatch){

            list($eadmin_class, $eadmin_function)  = Admin::getDispatchCall($dispatch);
            $this->auth($eadmin_class,$eadmin_function);
        }
        if (empty($confirm)) {
            $this->url($url)->params($data);
        } else {
            return $this->confirm($confirm, $url, $data)->type('warning');
        }
        return $this;
    }
    /**
     * 上传
     * @param string $url 上传url
     * @return Upload
     */
    public function upload($url){
        return Upload::create()->finder(false)
            ->attr('foreverShow',true)
            ->url((string)$url)
            ->chunk(false)
            ->disk('local')
            ->content($this);
    }
    /**
     * 复制
     * @param string $content 复制内容
     * @return $this
     */
    public function copy($content){
        $this->attr('data-clipboard-text',$content);
        return $this;
    }
    /**
     * 按钮文字
     * @param string $content 文字
     * @param string $name
     * @return Button
     */
    public function content($content, $name = 'default')
    {
        $this->content['default'] = [$content];
        return $this;
    }
}
