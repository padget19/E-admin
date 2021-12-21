<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-10-19
 * Time: 22:01
 */

namespace Eadmin\component\basic;


use think\exception\HttpResponseException;

/**
 * 通知
 * Class Notification
 * @package Eadmin\component\basic;
 */
class Notification
{
    protected $data = [];

    /**
     * 成功提示
     * @param string $title 标题
     * @param string $message 提示信息
     * @param string $url 跳转url
     * @return $this
     */
    public function success($title = '操作完成', $message = '数据更新成功', $url = '')
    {
        $this->response($title, $message, 'success', $url);
        return $this;
    }

    /**
     * 警告提示
     * @param string $title 标题
     * @param string $message 提示信息
     * @param string $url 跳转url
     * @return $this
     */
    public function warning($title, $message, $url = '')
    {
        $this->response($title, $message, 'warning', $url);
        return $this;
    }

    /**
     * 信息提示
     * @param string $title 标题
     * @param string $message 提示信息
     * @param string $url 跳转url
     * @return $this
     */
    public function info($title, $message, $url = '')
    {
        $this->response($title, $message, 'info', $url);
        return $this;
    }

    /**
     * 错误提示
     * @param string $title 标题
     * @param string $message 提示信息
     * @param string $url 跳转url
     * @return $this
     */
    public function error($title = '操作失败', $message = '数据保存失败', $url = '')
    {
        $this->response($title, $message, 'error', $url);
        return $this;
    }

    /**
     * 跳转url
     * @param string $url
     * @return $this
     */
    public function redirect($url)
    {
        $this->data['url'] = $url;
        return $this;
    }
    /**
     * 跳转url
     * @return $this
     */
    public function back()
    {
        $this->data['url'] = 'back';
        return $this;
    }
    /**
     * 刷新当前页面
     */
    public function refresh()
    {
        $this->data['refresh'] = true;
        return $this;
    }
    /**
     * 改变bind字段数据
     * @param array $data
     * @return $this
     */
    public function bind(array $data){
        $this->data['proxyData'] = $data;
        return $this;
    }
    public function data(array $data)
    {
        $this->data = array_merge($this->data, ['data' => $data]);
    }

    protected function response($title, $message, $type, $url = '')
    {
        $this->data = [
            'code'    => 80021,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'url'     => $url
        ];
    }

    public function __destruct()
    {
        throw new HttpResponseException(json($this->data));
    }
}
