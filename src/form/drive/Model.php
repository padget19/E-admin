<?php

namespace Eadmin\form\drive;

use Eadmin\contract\FormInterface;
use Eadmin\model\AdminModel;
use think\exception\HttpResponseException;
use think\facade\Db;
use think\facade\Log;
use think\model\relation\BelongsTo;
use think\model\relation\BelongsToMany;
use think\model\relation\HasMany;
use think\model\relation\HasOne;
use think\model\relation\MorphMany;
use think\model\relation\MorphOne;

/**
 * Class Model
 * @package Eadmin\form\drive
 * @property \think\Model $model
 */
class Model implements FormInterface
{
    //模型
    protected $model;
    //主键字段
    protected $pkField;
    //数据源
    protected $data = [];

    public function __construct($data)
    {
        $this->model   = $data;
        $this->pkField = $this->model->getPk();
    }

    /**
     * 保存数据
     * @param array $data 数据
     * @return bool
     * @throws \Exception
     */
    public function save(array $data)
    {
        $result = true;
        Db::startTrans();
        try {
            if (isset($data[$this->pkField])) {
                $isExists = Db::name($this->model->getTable())->where($this->pkField, $data[$this->pkField])->find();
                if ($isExists) {
                    $this->data  = $this->model->where($this->pkField, $data[$this->pkField])->find();
                    $this->model = $this->model->where($this->pkField, $data[$this->pkField])->find();
                }
            }
            $result = $this->model->save($data);
            foreach ($data as $field => $value) {
                if (method_exists($this->model, $field)) {
                    //多对多关联保存
                    if ($this->model->$field() instanceof BelongsToMany) {
                        $relationData = $value;
                        $this->model->$field()->detach();
                        if (is_string($relationData)) {
                            $relationData = explode(',', $relationData);
                            $relationData = array_filter($relationData);
                        }
                        if (count($relationData) > 0) {
                            $this->model->$field()->saveAll($relationData);
                        }
                    } elseif ($this->model->$field() instanceof HasOne || $this->model->$field() instanceof BelongsTo || $this->model->$field() instanceof MorphOne) {
                        $relationData = $data[$field];
                        if (!isset($data[$this->pkField]) || empty($this->data->$field)) {
                            $this->model->$field()->save($relationData);
                        } else {
                            $this->data->$field->save($relationData);
                        }

                    } elseif ($this->model->$field() instanceof HasMany || $this->model->$field() instanceof MorphMany) {
                        $realtionUpdateIds = array_column($value, 'id');
                        if (!empty($this->data->$field)) {
                            $deleteIds = $this->data->$field->column('id');

                            if (is_array($realtionUpdateIds)) {
                                $deleteIds = array_diff($deleteIds, $realtionUpdateIds);
                            }
                            if (count($deleteIds) > 0) {
                                $result = $this->model->$field()->whereIn($this->pkField, $deleteIds)->delete();
                            }
                        }
                        $foreignKey = $this->model->$field()->getForeignKey();
                        $localKey = $this->model->$field()->getLocalKey();
                        $parent =  $this->model->$field()->getParent();
                        foreach ($value as $key => &$val) {
                            $val['sort'] = $key;
                            $val[$foreignKey] = $parent->$localKey;
                        }
                        if (!empty($value)) {
                            $this->model->$field()->getModel()->saveAll($value);
                        }
                    }
                }
            }

            Db::commit();
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (\Exception $e) {
            Db::rollback();
            if (env('APP_DEBUG')) {
                throw $e;
            } else {
                Log::error($e->getMessage());
            }
            $result = false;
        }
        return $result;
    }

    /**
     * 批量保存
     * @param array $data
     * @return bool|\think\Collection
     */
    public function saveAll(array $data){
        try {
            $result = $this->model->saveAll($data);
        }catch (HttpResponseException $e) {
            throw $e;
        }catch (\Exception $e) {
            $result = false;
        }
        return $result;
    }
    public function getDataAll(){
        return $this->model->select();
    }
    /**
     * 获取字段数据
     * @param string|null $field
     * @return array|mixed|null
     */
    public function getData(string $field = null, $data = null)
    {
        if (is_null($data)) {
            $data = $this->data;
        }
        if (is_null($field)) {
            return $data;
        } else {
            if (method_exists($this->model, $field)) {
                if ($this->model->$field() instanceof BelongsToMany) {
                    if (empty($data->$field)) {
                        $relationData = null;
                    } else {
                        $relationData = $data->$field;
                    }
                    if (is_null($relationData)) {
                        $val = [];
                    } else {
                        $val = $relationData->column($this->pkField);
                    }
                    return $val;
                } elseif ($this->model->$field() instanceof HasMany) {
                    if (empty($data->$field)) {
                        return [];
                    } else {
                        return $data->$field;
                    }
                }
            }
            foreach (explode('.', $field) as $f) {
                if (isset($data[$f])) {
                    $data = $data[$f];
                } else {
                    $data = null;
                }
            }
            return $data;
        }
    }

    public function getPk()
    {
        return $this->pkField;
    }

    /**
     * 设置主键字段
     * @param $field
     */
    public function setPkField(string $field)
    {
        $this->pkField = $field;
    }
    public function model(){
        return $this->model;
    }
    public function edit($id)
    {
        $this->data = $this->model->db(null)->where($this->pkField, $id)->find();
    }
}
