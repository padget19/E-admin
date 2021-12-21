<?php


namespace Eadmin\component\grid;


use Eadmin\component\basic\DownloadFile;
use Eadmin\component\basic\Html;
use Eadmin\component\basic\Popover;
use Eadmin\component\basic\Space;
use Eadmin\component\basic\Tag;
use Eadmin\component\basic\Tip;
use Eadmin\component\basic\Tooltip;
use Eadmin\component\basic\Video;
use Eadmin\component\Component;
use Eadmin\component\form\field\Rate;
use Eadmin\component\form\field\Switchs;
use Eadmin\component\layout\Content;
use Eadmin\grid\Grid;

/**
 * Class Column
 * @link    https://element-plus.gitee.io/#/zh-CN/component/table
 * @package Eadmin\component\grid
 * @method $this dataIndex(string $value) 对应列内容的字段名
 * @method $this align(string $value)    left/center/right
 * @method $this header(string $value)    自定义内容
 * @method $this headerAlign(string $value)    left/center/right
 * @method $this fixed(string $value) true, left, right
 * @method $this width(int $value) 对应列的宽度
 */
class Column extends Component
{
    protected $name = 'ElTableColumn';
    protected $prop;
    protected $closure = null;
    //内容颜射
    protected $usings = [];
    //映射标签颜色
    protected $tagColor = [];
    //映射标签颜色主题
    protected $tagTheme = 'light';
    protected $tag = null;
    protected $grid;
    protected $tip = false;
    protected $hide = false;
    protected $exportClosure = null;
    protected $exportData;
    protected $total = 0;
    protected $totalRow = false;

    public function __construct($prop, $label, $grid)
    {
        $this->attr('slots', ['title' => 'eadmin_' . $prop, 'customRender' => 'default']);
        if (!empty($prop)) {
            $this->prop = $prop;
            $this->prop($prop);
            $this->dataIndex($prop);
            $this->key($prop);
        }
        if (!empty($label)) {
            $this->label($label);
        }
        $this->grid = $grid;
    }

    /**
     * 评分显示
     * @param int $max 最大长度
     * @return $this
     */
    public function rate($max = 5)
    {
        $this->display(function ($val) use ($max) {
            return Rate::create(null, floatval($val))->max(floatval($max))->disabled();
        });
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
     * 自定义导出
     * @param \Closure $closure
     */
    public function export(\Closure $closure)
    {
        $this->exportClosure = $closure;
        return $this;
    }

    /**
     * 关闭当前列导出
     * @return $this
     */
    public function closeExport()
    {
        $this->attr('closeExport', true);
        return $this;
    }

    /**
     * 开启排序
     * @return $this
     */
    public function sortable()
    {
        $this->attr('sorter', true);
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

    public function getField()
    {
        return $this->prop;
    }

    public function getUsing()
    {
        return $this->usings;
    }

    /**
     * 获取当前列字段数据
     * @param array $data 行数据
     * @return string
     */
    private function getData($data)
    {
        $prop = $this->attr('prop');
        $fields = explode('.', $prop);
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $data = $data[$field];
            } else {
                $data = null;
            }
        }
        return $data;
    }

    /**
     * 解析每行数据
     * @param array $data 数据
     * @return Html
     */
    public function row($data)
    {
        //获取当前列字段数据
        $originValue = $this->getData($data);
        if (is_null($originValue)) {
            //空默认占位符
            $value = '--';
        } else {
            $value = $originValue;
        }
        $this->exportData = $value;
        //映射内容颜色处理
        if (count($this->tagColor) > 0 && isset($this->tagColor[$value])) {
            $this->tag($this->tagColor[$value], $this->tagTheme);
        }
        //映射内容处理
        if (count($this->usings) > 0 && isset($this->usings[$value])) {
            $value = $this->usings[$value];
            $this->exportData = $value;
        }
        //是否显示标签
        if (!is_null($this->tag)) {
            $tag = clone $this->tag;
            $value = $tag->content($value);

        }
        //自定义内容显示处理
        if (!is_null($this->closure)) {
            $clone = clone $this;
            $value = call_user_func_array($this->closure, [$originValue, $data, $clone]);
            if ($value instanceof self) {
                $value = call_user_func_array($clone->getClosure(), [$originValue, $data, $clone]);
            }
            if (is_string($value) || is_numeric($value)) {
                $this->exportData = $value;
            }
        }
        //统计行
        if ($this->totalRow && is_numeric($value)) {
            $this->total += $value;
        }
        //自定义导出
        if (!is_null($this->exportClosure)) {
            $value = call_user_func_array($this->exportClosure, [$originValue, $data]);
            $this->exportData = $value;
        }
        //内容过长超出tip显示
        if ($this->tip) {
            if (!$this->attr('width')) {
                $this->width(120);
            }
            return Tip::create(Html::create($value)
                ->style([
                    'width' => $this->attr('width') . 'px',
                    'textOverflow' => 'ellipsis',
                    'overflow' => 'hidden',
                    'whiteSpace' => 'nowrap',
                ])
                ->tag('div'))->content($value)->placement('top');
        } else {
            return Html::create()->content($value);
        }
    }

    public function getExportData()
    {
        return $this->exportData;
    }

    /**
     * 显示的标题
     * @param string $label
     * @return $this
     */
    public function label(string $label)
    {
        $this->attr('label', $label);
        $this->header(Html::create()->content($label));
        return $this;
    }
   
    /**
     * 隐藏
     * @return \Eadmin\grid\Column|$this
     */
    public function hide()
    {
        $this->hide = true;
        $this->attr('hide', true);
        return $this;
    }

    public function isHide()
    {
        return $this->hide;
    }

    /**
     * 内容映射
     * @param array $usings 映射内容
     * @param array $tagColor 标签颜色
     * @param string tagTheme 标签颜色主题：dark，light，plain
     */
    public function using(array $usings, array $tagColor = [], $tagTheme = 'light')
    {
        $this->tagColor = $tagColor;
        $this->tagTheme = $tagTheme;
        $this->usings = $usings;
        return $this;
    }

    /**
     * 音频显示
     * @return $this
     */
    public function audio(){
        $this->display(function ($val){
            return "<audio controls src='{$val}'>您的浏览器不支持 audio 标签。</audio>";
        });
        return $this;
    }
    /**
     * 视频显示
     * @param int|string $width 宽度
     * @param int|string $height 高度
     * @return $this
     */
    public function video($width = 200, $height = 100)
    {
        $this->display(function ($val) use ($width, $height) {
            $video = new Video;
            $video->url($val)->size($width, $height);
            return $video;
        });
        return $this;
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
            $html = '';
            $jsonImage = json_encode($images);
            if ($multi) {
                foreach ($images as $image) {
                    $html .= "<el-image 
									style='width: {$width}px; height: {$height}px; border-radius: {$radius}%' 
									src='{$image}' 
									fit='cover' 
									:preview-src-list='{$jsonImage}'
							  ></el-image>&nbsp;";
                }
            } else {
                $html = "<el-image 
							style='width: {$width}px; height: {$height}px;border-radius: {$radius}%' 
							src='{$images[0]}' 
							fit='cover' 
							:preview-src-list='{$jsonImage}'
						 ></el-image>&nbsp;";
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
     * switch开关组
     * @param array $fields 字段
     * @return $this
     */
    public function switchGroup(array $fields)
    {
        return $this->display(function ($val, $data) use ($fields) {
            $params = $this->grid->getCallMethod();
            $params['eadmin_ids'] = [$data[$this->grid->drive()->getPk()]];
            $content = [];
            foreach ($fields as $field=>$label){
                $switch = Switchs::create(null, $data[$field])
                    ->state([[1 => ''], [0 => '']])
                    ->url('/eadmin/batch.rest')
                    ->field($field)
                    ->params($params);
                $content[] =  Html::create([
                    Html::create($label . ': '),
                    $switch
                ])->style(['display'=>'flex','justifyContent'=>'space-between'])->tag('p');
            }
            return $content;
        });
    }

    /**
     * switch开关
     * @param array $switchArr 二维数组 开启的在下标0 关闭的在下标1
     *                         $arr = [
     *                            [1 => '开启'],
     *                            [0 => '关闭'],
     *                         ];
     */
    public function switch($switchArr = [[1 => '开启'], [0 => '关闭']])
    {
        return $this->display(function ($val, $data) use ($switchArr) {

            $params = $this->grid->getCallMethod();
            $params['eadmin_ids'] = [$data[$this->grid->drive()->getPk()]];
            return Switchs::create(null, $val)
                ->state($switchArr)
                ->url('/eadmin/batch.rest')
                ->field($this->prop)
                ->params($params);
        });
    }

    /**
     * switch开关Html::create中直接使用
     * @param string $text 开关名称
     * @param string $field 开关的字段
     * @param array $data 当前行的数据
     * @param array $switchArr 二维数组 开启的在下标0 关闭的在下标1
     *                          $arr = [
     *                              [1 => '开启'],
     *                              [0 => '关闭'],
     *                          ];
     * @return Html
     */
    public function switchHtml($text, $field, $data, $switchArr = [[1 => '开启'], [0 => '关闭']])
    {
        $params = $this->grid->getCallMethod();
        $params['eadmin_ids'] = [$data[$this->grid->drive()->getPk()]];
        if (!empty($text)) $text .= "：";
        return Html::create([
        	$text,
            Switchs::create(null, $data[$field])
                ->state($switchArr)
                ->url('/eadmin/batch.rest')
                ->field($field)
                ->params($params),
        ])->tag('p');
    }

    /**
     * 合计行
     * @return $this
     */
    public function total()
    {
        $this->totalRow = true;
        return $this;
    }

    public function getTotal()
    {
        return $this->totalRow ? $this->total : false;
    }

    /**
     * 文件显示
     * @return $this
     */
    public function file()
    {
        return $this->display(function ($vals) {
            if (is_string($vals)) {
                $vals = [$vals];
            }
            $html = Html::create()->tag('div');
            foreach ($vals as $val) {
                $file = new DownloadFile();
                $file->url($val);
                $html->content($file);
            }
            return $html;
        });
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

    public function getClosure()
    {
        return $this->closure;
    }

}
