<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-10-06
 * Time: 10:35
 */

namespace Eadmin\form;


use Eadmin\form\event\Validating;
use think\exception\HttpResponseException;
use think\facade\Event;
use think\facade\Request;
use think\facade\Validate;

class ValidatorForm
{

    //创建验证规则
    protected $createRules = [
        'rule' => [],
        'msg'  => [],
    ];
    //更新验证规则
    protected $updateRules = [
        'rule' => [],
        'msg'  => [],
    ];
    protected $tabFields = [];
    public function setTabField($name,$field){
        $this->tabFields[] = ['name'=>$name,'field'=>$field];
    }
    public function getTabField(){
        return $this->tabFields;
    }
    /**
     * 表单新增更新验证规则
     * @Author: rocky
     * 2019/8/9 10:50
     * @param string $field 字段
     * @param array $rule 验证规则
     */
    public function rule(string $field, array $rule)
    {
        $this->parseRule($field, $rule, 1);
        $this->parseRule($field, $rule, 2);
        return $this;
    }

    /**
     * 表单新增验证规则
     * @Author: rocky
     * 2019/8/9 10:50
     * @param string $field 字段
     * @param array $rule 验证规则
     */
    public function createRule(string $field, array $rule)
    {
        $this->parseRule($field, $rule, 1);
        return $this;
    }

    /**
     * 表单更新验证规则
     * @Author: rocky
     * 2019/8/9 10:50
     * @param string $field 字段
     * @param array $rule 验证规则
     */
    public function updateRule(string $field, array $rule)
    {
        $this->parseRule($field, $rule, 2);
        return $this;
    }

    /**
     * 设置表单验证规则
     * @Author: rocky
     * 2019/8/9 10:45
     * @param array $rule 验证规则
     * @param string $msg 验证提示
     * @param int $type 1新增，2更新
     */
    public function setRules($rule, $msg, $type)
    {
        switch ($type) {
            case 1:
                $this->createRules['rule'] = array_merge_recursive($this->createRules['rule'], $rule);
                $this->createRules['msg']  = array_merge_recursive($this->createRules['msg'], $msg);
                break;
            case 2:
                $this->updateRules['rule'] = array_merge_recursive($this->updateRules['rule'], $rule);
                $this->updateRules['msg']  = array_merge_recursive($this->updateRules['msg'], $msg);
                break;
        }


    }

    /**
     * 生成验证规则
     * @param string $field 字段
     * @param array $rules
     * @param int $type 1新增，2更新
     */
    public function parseRule($field, $rules, $type)
    {
        $ruleMsg = [];
        $rule    = [];
        foreach ($rules as $key => $value) {
            if (strpos($key, ':') !== false) {
                $msgKey = $field . '.' . substr($key, 0, strpos($key, ':'));
            } else {
                $msgKey = $field . '.' . $key;

            }
            $ruleMsg[$msgKey] = $value;
            $rule[]           = $key;
        }
        $resRule = [
            $field => $rule
        ];
        $this->setRules($resRule, $ruleMsg, $type);
    }
    protected function getTabIndex($fields){
        $fields = array_keys($fields);
        foreach ($this->tabFields as $row){
            if(in_array($row['field'], $fields)){
                return $row['name'];
            }
        }
        return null;
    }
    /**
     * 验证表单规则
     * @param array $data
     * @param int $type 1新增，2更新
     */
    public function check($data, $type)
    {

        if ($type == 1) {
            //新增
            $validate = Validate::rule($this->createRules['rule'])->message($this->createRules['msg']);
            $rules    = $this->createRules['rule'];
        } else {
            //更新
            $validate = Validate::rule($this->updateRules['rule'])->message($this->updateRules['msg']);
            $rules    = $this->updateRules['rule'];
        }
        foreach ($data as $field => $arr) {
            if (is_array($arr) && count($arr) != count($arr, 1)) {
                $validateFields = [];
                $removeFields   = [];
                $manyValidate   = clone $validate;
                $current = current($arr);
                //循环验证规则
                foreach ($rules as $key => $rule) {
                    //查找带.的验证规则
                    if (strstr($key, $field . '.')) {
                        list($relation,$f) = explode('.',$key);
                        //查找二维数组中存在的验证字段进行单独验证，并移除原先存在的验证规则
                        if(is_array($current) && array_key_exists($f,$current)){
                            $validateFields[]   = $key;
                            $removeFields[$key] = true;
                        }
                    }
                }
                if ($validateFields) {
                    //二维数组中的验证字段进行单独验证
                    foreach ($arr as $index => $value) {
                        $validateData[$field] = $value;
                        $result               = $manyValidate->only($validateFields)->batch(true)->check($validateData);;
                        if (!$result) {
                            throw new HttpResponseException(json([
                                'code'    => 422,
                                'message' => '表单验证失败',
                                'data'    => $manyValidate->getError(),
                                'index'   => (string)$index,
                                'tabIndex'=>$this->getTabIndex($manyValidate->getError())
                            ]));
                        }
                    }
                }
                //移除判定二维数组中的验证字段
                $validate->remove($removeFields);
            }
        }
        Event::until(Validating::class,$data);
        $result = $validate->batch(true)->check($data);
        if(Request::has('eadmin_validate') && $result){
            throw new HttpResponseException(json([
                'code'    => 412,
                'message' => '验证通过',
                'data'    => []
            ]));
        }
        if (!$result) {
            throw new HttpResponseException(json([
                'code'    => 422,
                'message' => '表单验证失败',
                'data'    => $validate->getError(),
                'tabIndex'=>$this->getTabIndex($validate->getError())
            ]));
        }
    }
}
