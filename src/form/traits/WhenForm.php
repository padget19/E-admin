<?php


namespace Eadmin\form\traits;

use Eadmin\component\form\FormItem;

/**
 * Trait WhenForm
 * @package Eadmin\form\traits
 * @property FormItem $formItem
 */
trait WhenForm
{
    /**
     *
     * @param $operator
     * @param $value
     * @param null $closure
     * @return $this
     */
    public function when($operator, $value, $closure = null)
    {
        if (func_num_args() == 2) {
            $closure  = $value;
            $value    = $operator;
            $operator = '=';
        }
        if ($operator == 'in') {
            $operator = '=';
        }
        $formItems = $this->formItem->form()->collectFields($closure);
        if ($this->formItem->form()->manyRelation()) {
            $formField = '';
        } else {
            $formField = $this->formItem->form()->bindAttr('model') . '.';
        }
        $field = $formField . $this->bindAttr('modelValue');
        foreach ($formItems as $formItem) {
            $formItem->where(function($where) use($value,$operator,$field){
                if (is_array($value)) {
                    foreach ($value as $val) {
                        if ($operator == 'notIn') {
                            $where->where($field, $operator, $val);
                        } else {
                            $where->whereOr($field, $operator, $val);
                        }
                    }

                } else {
                    $where->where($field, $operator, $value);
                }
            });
            $this->formItem->form()->push($formItem);
        }
        return $this;
    }
}
