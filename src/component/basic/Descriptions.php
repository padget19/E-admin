<?php


namespace Eadmin\component\basic;

use Eadmin\component\Component;

/**
 * 描述列表
 * @method $this title(string $title) 标题文本，显示在左上方
 * @method $this border(bool $is_border = false) 是否带有边框
 * @method $this column(int $num = 3) 一行 Descriptions Item 的数量
 * @method $this direction(string $direction = 'horizontal') 排列的方向 vertical / horizontal
 * @method $this size(string $size) 列表的尺寸 medium / small / mini
 * @method $this extra(string $extra) 操作区文本，显示在右上方
 * @link https://element-plus.gitee.io/#/zh-CN/component/descriptions
 * Class Descriptions
 * @package Eadmin\component\basic
 */
class Descriptions extends Component
{
	protected $name = 'ElDescriptions';

	public static function create()
	{
		return new self();
	}

	/**
	 * @param string $title   标题
	 * @param mixed  $content 内容
	 * @param mixed  $name    与选项卡绑定值
	 * @return $this
	 */
	public function item($title, $content, $name = null)
	{
		$item = new DescriptionsItem();
		if (is_null($name)) {
			$name = $this->getContentCount();
		}
		// $item->name($name);
		$item->content($title, 'label');
		$item->content($content);
		$this->content($item);
		return $this;
	}
}