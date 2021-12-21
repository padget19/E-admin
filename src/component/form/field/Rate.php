<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

/**
 * 评分
 * Class Rate
 * @link https://element-plus.gitee.io/#/zh-CN/component/rate
 * @method $this max(int $num) 最大分值
 * @method $this allowHalf(bool $value = true) 是否允许半选
 * @method $this lowThreshold(int $num) 低分和中等分数的界限值，值本身被划分在低分中
 * @method $this highThreshold(int $num) 高分和中等分数的界限值，值本身被划分在高分中
 * @method $this colors(array $color) icon 的颜色。若传入数组，共有 3 个元素，为 3 个分段所对应的颜色；若传入对象，可自定义分段，键名为分段的界限值，键值为对应的颜色
 * @method $this voidColor(string $color) 未选中 icon 的颜色
 * @method $this disabledVoidColor(string $color) 只读时未选中 icon 的颜色
 * @method $this iconClasses(array $class) icon 的类名。若传入数组，共有 3 个元素，为 3 个分段所对应的类名；若传入对象，可自定义分段，键名为分段的界限值，键值为对应的类名
 * @method $this voidIconClass(string $class) 未选中 icon 的类名
 * @method $this disabledVoidIconClass(string $class) 只读时未选中 icon 的类名
 * @method $this showText(bool $value = true) 只是否显示辅助文字，若为真，则会从 texts 数组中选取当前分数对应的文字内容
 * @method $this showScore(bool $value = true) 是否显示当前分数，show-score 和 show-text 不能同时为真
 * @method $this textColor(string $color) 辅助文字的颜色
 * @method $this texts(array $text) 辅助文字数组
 * @method $this scoreTemplate(string $template) 分数显示模板
 * @package Eadmin\component\form\field
 */
class Rate extends Field
{
    protected $name = 'ElRate';

    public function __construct($field = null, $value = '')
    {
        if (empty($value)) {
            $value = 0;
        }
        parent::__construct($field, $value);
    }
}
