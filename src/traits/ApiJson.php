<?php
/**
 * @Author: rocky
 * @Copyright: 广州拓冠科技 <http://my8m.com>
 * Date: 2019/7/11
 * Time: 15:48
 */


namespace Eadmin\traits;


use app\common\service\ApiCode;
use think\exception\HttpResponseException;


trait  ApiJson
{
    /**
     * 返回成功json
     * @Author: rocky
     * 2019/7/11 16:02
     * @param array $data 输出数据
     * @param int $code 错误代码
     * @param string $msg 提示信息
     * @return \think\response\Json
     */
    public function successCode($data = [], $code = 200, $msg = '')
    {
        $response = $this->responseJsonData($data, $code, $msg);
        throw new HttpResponseException($response);
    }

    /**
     * 返回失败json
     * @Author: rocky
     * 2019/7/11 16:02
     * @param int $code 错误代码
     * @param string $msg 错误信息
     * @param array $data 输出数据
     * @param int $http_code http状态码
     * @return \think\response\Json
     */
    public function errorCode($code = 999, $msg = '', $data = [], $http_code = 200)
    {
        $response = $this->responseJsonData($data, $code, $msg, $http_code);
        throw new HttpResponseException($response);
    }

	/**
	 * 输出自定义json
	 * @Author: rocky
	 * 2019/7/12 17:08
	 * @param array $data 数据
	 * @param int $code 错误码
	 * @param string $errMsg 错误信息
	 * @param int $http_code 状态码
	 */
	protected function responseJsonData($data = [], $code = 200, $errMsg = '', $http_code = 200)
	{
		$return['code'] = (int)$code;
		if (!empty($errMsg)) {
			$return['message'] = $errMsg;
		} else {
			$message           = isset(config('apiCode')[$code]) ? config('apiCode')[$code] : '';
			$return['message'] = $message;
		}
		$return['data'] = $data;
		return json($return, $http_code);
	}

	/**
	 * 判断是否是空数组/集合
	 * @param mixed $data 数据
	 * @param int $code 状态码
	 * @return int
	 */
	protected function validates($data, $code)
	{
		if ((is_string($data) && empty($data)) ||
			count($data) < 1) {
			$code = 4004;
		}
		return $code;
	}
}
