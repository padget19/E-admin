<?php


namespace Eadmin\component\grid;


use Eadmin\component\Component;

/**
 * 分页
 * Class Pagination
 * @package Eadmin\component\grid
 * @method $this small(bool $value = true) 是否使用小型分页样式
 * @method $this background(bool $value = true) 是否为分页按钮添加背景色
 * @method $this pageSize(int $value) 每页显示条目个数
 * @method $this total(int $value) 总条目数
 * @method $this pagerCount(int $value) 页码按钮的数量，当总页数超过该值时会折叠
 * @method $this currentPage(int $value) 当前页数
 * @method $this layout(string $value) 组件布局 sizes, prev, pager, next, jumper, ->, total, slot
 * @method $this prevText(string $value) 替代图标显示的上一页文字
 * @method $this nextText(string $value) 替代图标显示的上一页文字
 * @method $this pageSizes(array $value) [10, 20, 30, 40, 50, 100]
 * @method $this disabled(bool $vlaue) 禁用
 * @method $this hideOnSinglePage(bool $vlaue) 只有一页时是否隐藏
 */
class Pagination extends Component
{
    protected $name = 'ElPagination';
}
