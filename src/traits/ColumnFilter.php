<?php

namespace Eadmin\traits;

use Eadmin\component\basic\Html;
use Eadmin\grid\Filter;

/**
 * Trait ColumnFilter
 * @package Eadmin\traits
 * @method Filter filter($field = null) 等于查询
 * @method Filter filterNeq($field = null) 不等于查询
 * @method Filter filterGt($field = null) 大于查询
 * @method Filter filterElt($field = null) 小于等于查询
 * @method Filter filterLt($field = null) 小于查询
 * @method Filter filterGqt($field = null) 大于等于查询
 * @method Filter filterBetween($field = null) 区间查询
 * @method Filter filterNotBetween($field = null) NOT区间查询
 * @method Filter filterLike($field = null) 模糊查询
 * @method Filter filterJson($node, $field = null) json查询
 * @method Filter filterJsonLike($node, $field = null) json模糊查询
 * @method Filter filterJsonArrLike($node, $field = null) json数组模糊查询
 * @method Filter filterIn($field = null) in查询
 * @method Filter filterNotIn($field = null) not in查询
 * @method Filter filterFindIn($field = null) findIn查询
 * @method Filter filterDate($field = null) 日期筛选
 * @method Filter filterTime($field = null) 时间筛选
 * @method Filter filterDatetime($field = null) 日期时间筛选
 * @method Filter filterDatetimeRange($field = null) 日期时间范围筛选
 * @method Filter filterDateRange($field = null) 日期范围筛选
 * @method Filter filterTimeRange($field = null) 时间范围筛选
 * @method Filter filterYear($field = null) 年日期筛选
 * @method Filter filterMonth($field = null) 月日期筛选
 * @method Filter filterCascader(...$field) 级联筛选
 * @method Filter filterCheckbox($options,$field = null) 多选框筛选
 * @method Filter filterRadio($options,$field = null) 单选框筛选
 * @method Filter filterRadioButton($options,$field = null) 单选框按钮筛选
 * @method Filter filterSelect($options,$field = null) 下拉框筛选
 * @method Filter filterSelectGroup($options,$field = null) 下拉框分组筛选
 */
trait ColumnFilter
{
    
    
    /**
     * 筛选方法解析
     * @param string $method 方法
     * @param string $arguments 参数
     * @return Filter
     */
    public function filterMethod($method, $arguments)
    {
        $method = lcfirst($method);
        if (empty($method)) {
            $method = 'eq';
        }
        $filter = $this->grid->getFilter();
        $field = $this->prop;
        $arg_num = count($arguments);
        if ($method == 'cascader') {
            $filter->$method(...$arguments)->hide();
        } elseif ($method == 'checkbox') {
            if ($arg_num == 2) {
                $field = $arguments[1];
            }
            $checkbox = $filter->in($field)->checkbox($arguments[0])
                ->horizontal()
                ->onCheckAll();
        } elseif (strpos($method, 'select') === 0 || strpos($method, 'radio') === 0) {
            if ($arg_num == 2) {
                $field = $arguments[1];
            }
            $filter->eq($field)->$method($arguments[0])->popperAppendToBody(false);
        } elseif (strpos($method, 'json') === 0) {
            if ($arg_num == 1) {
                $node = $arguments[0];
            }
            if ($arg_num == 2) {
                $field = $arguments[1];
            }
            $filter->$method($field, $arguments[0]);
        } else {
            if ($arg_num == 1) {
                $field = $arguments[0];
            }
            $filter->$method($field);
        }
        $filter->hide();
        $this->attr('eadminFilterDropdown', Html::div()->content($filter->form()->getLastItem()->getContent('default')));
        $this->attr('slots', array_merge($this->attr('slots'), ['filterIcon' => 'filterIcon_' . $field, 'filterDropdown' => 'filterDropdown']));
        return $filter;
    }
}
