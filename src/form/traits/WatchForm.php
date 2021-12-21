<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-10-06
 * Time: 10:26
 */

namespace Eadmin\form\traits;

use Eadmin\traits\ApiJson;
use think\facade\Request;


/**
 * form数据监听
 * Trait Watch
 * @package Eadmin\form\traits
 */
trait WatchForm
{
    use ApiJson;
    protected $watchs = [];

    /**
     * 设置监听数据方法
     * @param array $data
     */
    public function watch(array $data)
    {
        $this->watchs = $data;
        $fields = array_keys($this->watchs);
        $this->attr('watch',$fields);
    }

    /**
     * 监听数据回调
     */
    protected function watchCall($data)
    {
        if (Request::has('eadmin_form_watch')) {
			$watch   = new \Eadmin\form\Watch($data['form']);
			$closure = $this->watchs[$data['field']];
			$watch->set($data['field'],$data['newValue']);
			call_user_func_array($closure, [$data['newValue'], $watch, $data['oldValue']]);
			$this->successCode([
				'form'      => $watch->get(),
				'showField' => $watch->getShowField(),
				'hideField' => $watch->getHideField(),
			]);
        }
    }
}
