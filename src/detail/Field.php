<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-02-11
 * Time: 10:05
 */

namespace Eadmin\detail;


use Eadmin\component\basic\Button;
use Eadmin\component\basic\DownloadFile;
use Eadmin\component\basic\Html;
use Eadmin\component\basic\Image;
use Eadmin\component\basic\Link;
use Eadmin\component\basic\Popover;
use Eadmin\component\basic\Tag;
use Eadmin\component\basic\Tip;
use Eadmin\component\basic\Video;
use Eadmin\component\Component;
use Eadmin\component\form\field\Rate;
use Eadmin\component\layout\Column;
use function Stringy\create;

class Field extends Column
{

    //内容颜射
    protected $usings = [];
    //映射标签颜色
    protected $tagColor = [];
    //映射标签颜色主题
    protected $tagTheme = 'light';
    protected $tag = null;
    protected $tip = false;
    protected $value = null;
    protected $data = null;
    protected $closure = null;

    public function __construct($label, $content, $data,$width)
    {
        $this->attr('style', [
            'fontSize'     => '14px',
            'borderBottom' => '1px solid rgb(240, 240, 240)',
            'padding'      => '13px 0',
            'display'=>'flex'
        ]);
        if (!empty($label)) {
            $label = Html::create($label . ':')->tag('div')
                ->attr('style', ['color' => '#888888', 'marginRight' => '5px','width'=>$width.'px','textAlign'=>'right']);
            $this->content($label);
        }
        $this->value = $content;
        $this->data  = $data;
    }

    /**
     * 显示图片
     * @param int $width 宽度
     * @param int $height 高度
     * @param int $radius 圆角
     * @param bool $multi 是否显示多图
     * @return $this
     */
    public function image($width = 80, $height = 80, $radius = 5, $multi = false)
    {
        $this->display(function ($val, $data) use ($width, $height, $radius, $multi) {
            if (empty($val)) {
                return '--';
            }
			$images = $val;
            if (is_string($val)) {
                $images = explode(',', $val);
            }
            $html      = Html::create();
            if ($multi) {
                foreach ($images as $image) {
                	$html->content(
                		Image::create()
							->fit('cover')
							->src($image)
							->previewSrcList($images)
							->style([
								'width' => "{$width}px",
								'height' => "{$height}px",
								'borderRadius' => "{$radius}%",
								'marginRight' => '5px'
							])
					);
                }
            } else {
				$html->content(
					Image::create()
						->fit('cover')
						->src($images[0])
						->previewSrcList($images)
						->style([
							'width' => "{$width}px",
							'height' => "{$height}px",
							'borderRadius' => "{$radius}%",
							'marginRight' => '5px'
						])
				);
            }
            return $html;
        });
        return $this;
    }

    /**
     * 显示多图片
     * @param int $width 宽度
     * @param int $height 高度
     * @param int $radius 圆角
     */
    public function images($width = 80, $height = 80, $radius = 5)
    {
        $this->image($width, $height, $radius, true);
    }

	/**
	 * 文字链接
	 * @link https://element-plus.gitee.io/#/zh-CN/component/link 文字链接
	 * @link https://element-plus.gitee.io/#/zh-CN/component/icon 图标
	 * @param string $field 字段，不指定则显示当前value
	 * @param string $target 打开方式 _blank(在新窗口中打开) / _self(在相同的窗口打开) / _parent(在父窗口打开) / _top(在整个窗口中)
	 * @param string $icon 图标
	 * @param string $type 类型
	 * @param bool   $underline 是否下划线
	 * @return $this
	 */
	public function link($field = '', $target = '_blank', $icon = '', $type = 'primary', $underline = false)
	{
		$this->display(function ($val, $data) use ($field, $target, $icon, $type, $underline) {
			$label = $field ? $data[$field] : $val;
			return Link::create($label)
				->href($val)
				->type($type)
				->underline($underline)
				->target($target)
				->icon($icon);
		});
		return $this;
	}

	/**
	 * 弹出框
	 * @param string $field 指定字段
	 * @param string $label 按钮名称
	 * @param string $width 宽度
	 * @param string $tigger 触发方式  click/focus/hover/manual
	 * @param string $placement 出现位置 top/top-start/top-end/bottom/bottom-start/bottom-end/left/left-start/left-end/right/right-start/right-end
	 */
	public function popover($field = '', $label = '查看', $width = '500px', $tigger = 'hover', $placement = 'top')
	{
		$this->display(function ($val, $data) use ($field, $label, $width, $tigger, $placement) {
			$valueData = $field ? $data[$field] : $val;
			if (empty($valueData)) return '';
			return Popover::create(Button::create($label))
				->content($this->getTags($valueData))
				->width($width)
				->trigger($tigger)
				->placement($placement);
		});
		return $this;
	}

    /**
     * 标签显示
     * @param string $color 标签颜色：success，info，warning，danger
     * @param string $theme 主题：dark，light，plain
     * @param string $size 尺寸:medium，small，mini
     */
    public function tag($color = '', $theme = 'dark', $size = 'mini')
    {
        $this->tag = Tag::create()->type($color)->size($size)->effect($theme);
        return $this;
    }

	/**
	 * 多个标签
	 * @param string $field 指定字段
	 * @param string $type 类型 success / info / warning / danger
	 * @param string $size 尺寸   medium / small / mini
	 * @return $this
	 */
	public function tags($field = '', $type = 'primary', $size = 'small')
	{
		$this->display(function ($val, $data) use ($field, $type, $size) {
			$valueData = $field ? $data[$field] : $val;
			if (empty($valueData) || !is_array($valueData)) return '';
			return $this->getTags($valueData, $type, $size);
		});
		return $this;
	}

	/**
	 * 标签组组装
	 * @param array  $value 数据
	 * @param string $type 类型 success / info / warning / danger
	 * @param string $size 尺寸    medium / small / mini
	 * @return Html
	 */
	public function getTags(array $value = [], $type = 'primary', $size = 'small')
	{
		$html = Html::create()
			->tag('div')
			->style(['display' => 'flex', 'flexWrap' => 'wrap']);
		foreach ($value as $apartment) {
			$html->content(
				Html::create(
					Tag::create($apartment)
						->type($type)
						->size($size)
				)
					->tag('div')
					->style(['marginRight' => '5px', 'marginBottom' => '5px'])
			);
		}
		return $html;
	}

    /**
     * 评分显示
     * @param int $max 最大长度
     * @return $this
     */
    public function rate($max = 5)
    {
        $this->display(function ($val) use ($max) {
            return Rate::create(null, $val)->max($max)->disabled();
        });
        return $this;
    }
    /**
     * 文件显示
     * @return $this
     */
    public function file()
    {
        return $this->display(function ($vals) {
            if(is_string($vals)){
                $vals = [$vals];
            }
            $html = Html::create()->tag('div');
            foreach ($vals as $val){
                $file = new DownloadFile();
                $file->url($val);
                $html->content($file);
            }
            return $html;
        });
    }

    /**
     * 内容映射
     * @param array $usings 映射内容
     * @param array $tagColor 标签颜色
     * @param string $tagTheme 标签颜色主题：dark，light，plain
     */
    public function using(array $usings, array $tagColor = [], $tagTheme = 'light')
    {
        $this->tagColor = $tagColor;
        $this->tagTheme = $tagTheme;
        $this->usings   = $usings;
        return $this;
    }

    /**
     * 文字提示
     * @return $this
     */
    public function tip()
    {
        $this->tip = true;
        return $this;
    }

    /**
     * 追加前面
     * @param mixed $prepend
     * @return Column
     */
    public function prepend($prepend)
    {
        return $this->display(function ($val) use ($prepend) {
            return $prepend . $val;
        });
    }

    /**
     * 视频显示
     * @param int|string $width 宽度
     * @param int|string $height 高度
     * @return $this
     */
    public function video($width = 400, $height = 200)
    {
        return $this->display(function ($val) use ($width, $height) {
            $video = new Video();
            $video->url($val)->size($width, $height);
            return $video;
        });
    }

    /**
     * 追加末尾
     * @param mixed $append
     * @return Column
     */
    public function append($append)
    {
        return $this->display(function ($val) use ($append) {
            return $val . $append;
        });
    }

    /**
     * 自定义显示
     * @param \Closure $closure
     * @return $this
     */
    public function display(\Closure $closure)
    {
        $this->closure = $closure;
        return $this;
    }

    /**
     * 占位
     * @param int $span 最大24
     * @return $this
     */
    public function md($span = 24)
    {
        $style = $this->attr('style');
        unset($style['borderBottom']);
        $this->style($style);
        $this->span($span);
        return $this;
    }

    public function jsonSerialize()
    {
        $originValue = $this->value;
        if (is_null($originValue)) {
            //空默认占位符
            $value = '--';
        } else {
            $value = $originValue;
        }
        //映射内容颜色处理
        if (count($this->tagColor) > 0  && isset($this->tagColor[$value])) {
            $this->tag($this->tagColor[$value], $this->tagTheme);
        }
        //映射内容处理
        if (count($this->usings) > 0 && isset($this->usings[$value])) {
            $value = $this->usings[$value];
        }
        //是否显示标签
        if (!is_null($this->tag)) {
            $value = $this->tag->content($value);
        }
        //自定义内容显示处理
        if (!is_null($this->closure)) {
            $value = call_user_func_array($this->closure, [$originValue, $this->data]);
        }
        if ($this->tip) {
            $value = Tip::create($value)->content($value)->placement('right');
        }

        $this->content(Html::create($value)->tag('div')->attr('style', ['flex'=>'1','lineHeight'=>'1.5']));
        return parent::jsonSerialize(); // TODO: Change the autogenerated stub
    }
}
