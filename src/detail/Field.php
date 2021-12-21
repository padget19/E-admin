<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-02-11
 * Time: 10:05
 */

namespace Eadmin\detail;


use Eadmin\component\basic\DownloadFile;
use Eadmin\component\basic\Html;
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
            if (is_string($val)) {
                $images = explode(',', $val);
            } elseif (is_array($val)) {
                $images = $val;
            }
            $html      = '';
            $jsonImage = json_encode($images);
            if ($multi) {
                foreach ($images as $image) {
                    $html .= "<el-image style='width: {$width}px; height: {$height}px;border-radius: {$radius}%' src='{$image}' fit='cover' :preview-src-list='{$jsonImage}'></el-image>&nbsp;";
                }
            } else {
                $html = "<el-image style='width: {$width}px; height: {$height}px;border-radius: {$radius}%' src='{$images[0]}' fit='cover' :preview-src-list='{$jsonImage}'></el-image>&nbsp;";
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
     * @return $this
     */
    public function images($width = 80, $height = 80, $radius = 5)
    {
        $this->image($width, $height, $radius, true);
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
