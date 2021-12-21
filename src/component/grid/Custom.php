<?php


namespace Eadmin\component\grid;

use Eadmin\component\Component;

/**
 * Class Custom
 * @package Eadmin\component\grid
 * @method $this header($content) 列表头部
 * @method $this footer($content) 列表底部
 * @method $this bordered($bool = true) 是否展示边框
 * @method $this split($bool = true) 是否展示分割线
 * @method $this itemLayout($value) 设置 List.Item 布局, 设置成 vertical 则竖直样式显示, 默认横排
 * @method $this size($value) default | middle | small
 */
class Custom extends Component
{
    /**
     * 列表栅格配置
     * @param int $gutter 栅格间隔
     * @param int $column 列数
     * @return $this
     */
    public function grid($gutter,$column){
        $sm = $column / 2;
        if ($sm < 1) {
            $sm = 1;
        }
        $xs = $column / 4;
        if ($xs < 1) {
            $xs = 1;
        }
        $this->attr('grid',['column'=>$column,'gutter'=>$gutter,'md'=>$column,'sm'=>$sm,'xs'=>$xs]);
        return $this;
    }
}
