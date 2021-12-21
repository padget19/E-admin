<?php


namespace Eadmin\component\basic;

use Eadmin\Admin;
use Eadmin\component\Component;
use think\app\Url;

/**
 * Class DropdownItem
 * @package Eadmin\component\basic
 * @method $this disabled(bool $bool = true) 禁用
 * @method $this url(string $value) ajax请求url
 * @method $this method(string $value) ajax请求method get / post /put / delete
 * @method $this params(array $value) 提交ajax参数
 */
class DropdownItem extends Component
{
    protected $name = 'EadminDropdownItem';
    protected $dropdown;
    public function __construct($content)
    {
        if($content){
            $this->content($content);
        }
    }

    public static function create($content){
        return new self($content);
    }
    public function dropdown(Dropdown $dropdown){
        $this->dropdown = $dropdown;
        return $this->dropdown;
    }
    /**
     * 模态对话框
     * @return Dialog
     */
    public function dialog()
    {
        $dialog  = Dialog::create();
        $dialog->bindValue(false,'show');
        $visible = $dialog->bindAttr('show');
        $this->event('click', [$visible => true]);
        $this->dropdown->content($dialog, 'reference');
        return $dialog;
    }

    /**
     * 保存数据
     * @param array $data
     * @param mixed $url
     * @param string $confirm
     * @param string $input 
     * @return $this
     */
    public function save(array $data, $url, $confirm = '',$input = false)
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
            $confirm       = Confirm::create($this->content['default'][0])
                ->message($confirm)->url($url)->params($data)->type('warning');
            if($input !== false){
                $confirm->input($input);
            }
            $this->content = [];
            $this->content($confirm);
        }
        return $this;
    }
}
