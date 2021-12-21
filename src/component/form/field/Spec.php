<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

/**
 * Class Spec
 * @package Eadmin\component\form\field
 * @method $this data(array $data) 规格数据
 */
class Spec extends Field
{
    protected $name = 'EadminSpec';

    /**
     * @param \Closure $closure
     * @param string $specGroupField 规格组保存字段
     * @return $this
     */
    public function table(\Closure $closure,$specGroupField = 'specs'){

        $originItemComponent = $this->formItem->form()->itemComponent();
        $this->formItem->form()->setItemComponent([]);
        $relation = $this->bindAttr('modelValue');
        $this->formItem->form()->manyRelation($relation);
        $this->formItem->form()->validatorBind($relation);
        $formItems = $this->formItem->form()->collectFields($closure);
        $this->formItem->form()->manyRelation('');
        $this->formItem->form()->setItemComponent($originItemComponent);
        foreach ($formItems as $formItem) {
            $columns[] = [
                'title'=>$formItem->attr('label'),
                'dataIndex'=>$formItem->attr('prop'),
                'prop'=>$formItem->attr('prop'),
                'component'=>$formItem
            ];
            $formItem->removeAttr('label');
        }
        $validatorField = $this->formItem->form()->bindAttr('model') . 'Error';
        $this->attr('validator', $validatorField);
        $this->attr('field',$relation);
        $this->attr('columns',$columns);
        $this->bindAttr('specs',$specGroupField,true);
        $this->bindAttr('specId','id');
        return $this;
    }
}
