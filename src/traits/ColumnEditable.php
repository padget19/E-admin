<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-10-18
 * Time: 19:53
 */

namespace Eadmin\traits;

use Eadmin\form\Form;
use Eadmin\form\traits\ComponentForm;
use Eadmin\component\basic\Html;

/**
 * 行内编辑
 */
trait ColumnEditable
{
    use ComponentForm;

    protected $editable = null;

    /**
     * @param string $type text输入框 textarea文本域 select下拉框 datetime日期时间 date日期 year年 month月 slider滑块 color颜色选择器 rate评分 checkbox多选框 radio单选框
     * @param array $options 选项数据 select|chexkbox|radio
     * @return $this
     * @throws \Exception
     */
    public function editable($type = 'text',$options = [])
    {
        $this->editable = [
            'type'=>$type,
            'options'=>$options
        ];
        $this->grid->updateing(function ($ids, $data) {
            if (isset($data['eadmin_editable'])) {
                $id = array_pop($ids);
                $form = new Form($this->grid->drive()->model());
                $data[$form->getDrive()->getPk()] = $id;
                $result = $form->getDrive()->save($data);
                if ($result !== false) {
                    $this->grid->model()->where($this->grid->drive()->getPk(),$id);
                    $this->grid->exec();
                    $data = $this->grid->parseData();
                    $row = array_pop($data);
                    admin_success('操作完成', '数据保存成功')->data($row);
                } else {
                    admin_error_message('数据保存失败');
                }
            }
        });
        return $this;
    }

    protected function editableCall($value, $data)
    {
        $params = $this->grid->getCallMethod();
        $id = $this->grid->drive()->getPk();
        $field = 'eadmin_editable' . $data[$id];
        $params['eadmin_ids'] = [$data[$id]];
        $params['eadmin_editable_bind'] = $field;
        $params['field'] = $this->prop;
        $params['eadmin_editable'] = true;
        $type = $this->editable['type'];
        $component = self::$component[$type];
        $component = $component::create(null, $data[$this->prop])->type($type)->changeAjax('/eadmin/batch.rest', $params, 'put');
        $component->bind($field, 0);
        if($type == 'select'){
            $component->popperAppendToBody(false);
        }elseif ($type == 'checkbox' || $type == 'radio') {
            $component->horizontal()
                ->onCheckAll();
        }
        if(!empty($this->editable['options'])){
            $component->options($this->editable['options']);
        }
        $component->where($field, 1)->directive('focus', $field)->attr('ref', $field);
        if($type != 'select'){
            $component->event('blur', [$field => 0]);
        }
        $html = Html::div()->content($value)->content(
            Html::create()->tag('i')->attr('class', ['el-icon-edit', 'editable-cell-icon'])->event('click', [$field => 1])
        )->attr('class', 'eadmin-editable-cell')->where($field, 0);
        return [$html, $component];
    }
}
