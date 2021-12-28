<?php


namespace Eadmin\component\basic;


use Eadmin\component\Component;

/**
 * 文字链接
 * Class Link
 * @link https://element-plus.gitee.io/#/zh-CN/component/link
 * @method $this type(string $type) 类型 primary / success / warning / danger / info
 * @method $this underline(bool $value = true) 是否下划线
 * @method $this icon(string $icon) 图标类名
 * @method $this href(string $href) 原生 href 属性
 * @package Eadmin\component\basic
 */
class Link extends Component
{
    protected $name = 'ElLink';
    public function __construct($content)
    {
        parent::__construct();
        $this->content(Html::create($content));
    }

	/**
	 * 打开方式
	 * @param string $attr _blank(在新窗口中打开) / _self(在相同的窗口打开) / _parent(在父窗口打开) / _top(在整个窗口中)
	 * @return $this
	 */
	public function target($attr = '_blank')
	{
		$this->attr('target', $attr);
		return $this;
	}

    public static function create($content)
    {
        return new self($content);
    }
}
