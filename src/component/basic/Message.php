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
 * 消息提示
 * Class Message
 * @package Eadmin\component\basic;
 */
class Message
{
    protected $data = [];

    /**
     * 成功提示
     * @param string $message 提示信息
     * @param string $url 跳转url
     * @return $this
     */
    public function success($message = '操作成功', $url = '')
    {
        $this->response($message, 'success', $url);
        return $this;
    }

    /**
     * 警告提示
     * @param string $message 提示信息
     * @param string $url 跳转url
     * @return $this
     */
    public function warning($message, $url = '')
    {
        $this->response($message, 'warning', $url);
        return $this;
    }

    /**
     * 信息提示
     * @param string $message 提示信息
     * @param string $url 跳转url
     * @return $this
     */
    public function info($message, $url = '')
    {
        $this->response($message, 'info', $url);
        return $this;
    }

    /**
     * 错误提示
     * @param string $message 提示信息
     * @param string $url 跳转url
     * @return $this
     */
    public function error($message = '数据保存失败', $url = '')
    {
        $this->response($message, 'error', $url);
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

    protected function response($message, $type, $url = '')
    {
        $this->data = [
            'code'    => 80020,
            'type'    => $type,
            'message' => $message,
            'url'     => $url
        ];
    }

    public function __destruct()
    {
        throw new HttpResponseException(json($this->data));
    }
}
