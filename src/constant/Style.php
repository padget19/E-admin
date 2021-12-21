<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-06-21
 * Time: 22:39
 */

namespace Eadmin\constant;

class Style
{
    /**
     * flex布局
     */
    public const FLEX = ['display'=>'flex'];
    /**
     * 垂直居中
     */
    public const FLEX_ITEMS_CENTER = ['alignItems' => 'center'];
    /**
     * 水平居中
     */
    public const FLEX_CONTENT_CENTER = ['alignContent' => 'center'];
    /**
     * 水平分布
     */
    public const FLEX_SPACE_BETWEEN = ['justifyContent' => 'space-between'];
    /**
     * 垂直方向
     */
    public const FLEX_DIRECTION_COLUMN = ['flexDirection' => 'column'];
    /**
     * flex水平垂直居中
     */
    public const FLEX_CENTER = ['display'=>'flex','alignItems' => 'center','alignContent' => 'center'];
    /**
     * flex水平分布垂直居中
     */
    public const FLEX_BETWEEN_CENTER = ['display'=>'flex','alignItems' => 'center','justifyContent' => 'space-between'];


}