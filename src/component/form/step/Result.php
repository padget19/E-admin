<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-06-26
 * Time: 17:31
 */

namespace Eadmin\component\form\step;


use Eadmin\component\basic\Button;
use think\exception\HttpResponseException;
use think\helper\Arr;

class Result
{
    protected $data;
    protected $result;
    protected $id = null;
    protected $isSuccess = false;
    public function __construct($data,$isSuccess,$id)
    {
        $this->data = $data;
        $this->id = $id;
        $this->isSuccess = $isSuccess;
    }

    /**
     * 是否保存成功
     * @return bool
     */
    public function isSuccess(){
        return $this->isSuccess;
    }
    /**
     * 获取保存成功id
     * @return null
     */
    public function getId(){
        return $this->id;
    }
    /**
     * 返回成功
     * @param string $title 标题
     * @param string  $content 内容
     * @return \Eadmin\component\basic\Result
     */
    public function sucess($title=null, $content='')
    {
        return $this->result($title ?? admin_trans('admin.operation_complete'), $content, 'success')->extra($this->resetButton()->typePrimary());
    }

    /**
     * 返回错误
     * @param string $title
     * @param string $content
     * @return \Eadmin\component\basic\Result
     */
    public function error($title, $content){
        return $this->result($title, $content, 'error');
    }

    /**
     * 返回警告
     * @param string $title
     * @param string $content
     * @return \Eadmin\component\basic\Result
     */
    public function warning($title, $content){
        return $this->result($title, $content, 'warning');
    }

    /**
     * 返回信息
     * @param string $title
     * @param string $content
     * @return \Eadmin\component\basic\Result
     */
    public function info($title, $content){
        return $this->result($title, $content, 'info');
    }

    /**
     * @param string $title
     * @param string $content
     * @param string $status
     * @return \Eadmin\component\basic\Result
     */
    public function result($title, $content, $status)
    {
        $this->result = \Eadmin\component\basic\Result::create()->status($status)
            ->content($title, 'title')
            ->content($content, 'subTitle')
            ->extra($this->resetButton());
        return $this->result;
    }

    /**
     * 获取提交数据
     * @param $field 字段
     * @return mixed
     */
    public function input($field)
    {
        return Arr::get($this->data, $field);
    }

    /**
     * 重新提交按钮
     * @param string $text
     * @return Button
     */
    public function resetButton($text = null)
    {
        return Button::create($text ?? admin_trans('admin.resubmit'))
            ->event('click', [
                $this->data['eadmin_step'] => 0,
                $this->data['eadmin_step_reset'] => true
            ]);
    }
    public function __destruct()
    {
        throw new HttpResponseException(json([
            'code'=>200,
            'type'=>'step',
            'data'=> $this->result
        ]));
    }
}
