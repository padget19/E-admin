<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-06-27
 * Time: 09:10
 */

namespace Eadmin\component\basic;


use Eadmin\component\Component;

/**
 * 分割线
 * Class Divider
 * @method $this direction($direction = 'horizontal') 设置分割线方向 horizontal / vertical
 * @method $this contentPosition($position = 'center') 设置分割线文案的位置 left / right / center
 * @package Eadmin\component\basic
 */
class Divider extends Component
{
	protected $name = 'ElDivider';

	public static function create()
	{
		return new static();
	}
}