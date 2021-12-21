<?php


namespace Eadmin\form\traits;


trait Validator
{
	/**
	 * 验证最多字符
	 * @param int    $num   数量
	 * @param string $text  文案，必须带上[字段]、[数量]
	 * @return $this
	 */
	public function maxRule($num = 100, $text = '[字段]不能超过[数量]字')
	{
		$this->formItem->rules([
			"max:$num" => str_replace([
				'[字段]',
				'[数量]',
			], [
				$this->formItem->attr('label'),
				$num,
			], $text),
		]);
		return $this;
	}

	/**
	 * 验证最少字符
	 * @param int    $num   字符
	 * @param string $text  文案，必须带上[字段]、[数量]
	 * @return $this
	 */
	public function minRule($num = 5, $text = '[字段]不能少于[数量]字')
	{
		$this->formItem->rules([
			"min:$num" => str_replace([
				'[字段]',
				'[数量]',
			], [
				$this->formItem->attr('label'),
				$num,
			], $text),
		]);
		return $this;
	}

	/**
	 * 验证邮件有效性
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function emailRule($text = '[字段]格式不正确')
	{
		$this->formItem->rules(['email' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证手机有效性
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function mobileRule($text = '[字段]格式不正确')
	{
		$this->formItem->rules(['mobile' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证唯一性
	 * @param string $table 表名,为空则为当前表
	 * @param string $field 字段，为空则当前字段
	 * @param string $text  文案，必须带上[字段]
	 * @return $this
	 */
	public function uniqueRule($table = '', $field = '', $text = '[字段]已重复')
	{
		$table = $table ?: $this->formItem->form()->getDrive()->model()->getTable();
		$field = $field ?: $this->formItem->attr('prop');
		$this->formItem->rules(["unique:{$table},{$field}" => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证身份证有效性
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function idCardRule($text = '[字段]不是有效的身份证')
	{
		$this->formItem->rules(['id_card' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证字段是否在某个区间
	 * @param int    $start 开始值
	 * @param int    $end   结束值
	 * @param string $text  文案，必须带上[开始]、[结束]
	 * @return $this
	 */
	public function betweenRule($start, $end, $text = '[字段]在[开始] - [结束]之间')
	{
		$this->formItem->rules([
			"between:{$start},{$end}" => str_replace([
				'[开始]',
				'[结束]',
				'[字段]',
			], [
				$start,
				$end,
				$this->formItem->attr('label')
			], $text),
		]);
		return $this;
	}

	/**
	 * 验证字段是否不在某个区间
	 * @param int    $start 开始值
	 * @param int    $end   结束值
	 * @param string $text  文案，必须带上[开始]、[结束]
	 * @return $this
	 */
	public function notBetweenRule($start, $end, $text = '[字段]不在[开始] - [结束]之间')
	{
		$this->formItem->rules([
			"notBetween:{$start},{$end}" => str_replace([
				'[开始]',
				'[结束]',
				'[字段]',
			], [
				$start,
				$end,
				$this->formItem->attr('label')
			], $text),
		]);
		return $this;
	}

	/**
	 * 验证输入值的是否不一致
	 * @param string $field        被验证字段
	 * @param string $confirmLabel 对比的文案
	 * @param string $text         文案，必须带上[被对比]、[对比]
	 * @return $this
	 */
	public function confirmRule($field = 'confirm_password', $confirmLabel = '重复密码', $text = '[被对比]和[对比]输入不一致')
	{
		$this->formItem->rules([
			"confirm:{$field}" => str_replace([
				'[被对比]',
				'[对比]',
			], [
				$this->formItem->attr('label'),
				$confirmLabel,
			], $text),
		]);
		return $this;
	}

	/**
	 * 验证输入值的是否一致
	 * @param string $field          被验证字段
	 * @param string $differentLabel 对比的文案
	 * @param string $text           文案，必须带上[被对比]、[对比]
	 * @return $this
	 */
	public function differentRule($field, $differentLabel, $text = '[被对比]和[对比]输入一致')
	{
		$this->formItem->rules([
			"different:{$field}" => str_replace([
				'[被对比]',
				'[对比]',
			], [
				$this->formItem->attr('label'),
				$differentLabel,
			], $text),
		]);
		return $this;
	}

	/**
	 * 验证是否是有效的URL
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function urlRule($text = '[字段]不是有效的url')
	{
		$this->formItem->rules(['url' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证是否是纯字母
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function alphaRule($text = '[字段]不是纯字母')
	{
		$this->formItem->rules(['alpha' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证是否是字母和数字
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function alphaNumRule($text = '[字段]不是纯字母和数字')
	{
		$this->formItem->rules(['alphaNum' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证是否是字母和数字，下划线_及破折号-
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function alphaDashRule($text = '[字段]不是字母和数字，下划线_及破折号-')
	{
		$this->formItem->rules(['alphaDash' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证是否是汉字
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function chsRule($text = '[字段]不是汉字')
	{
		$this->formItem->rules(['chs' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证是否是汉字、字母
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function chsAlphaRule($text = '[字段]不是汉字、字母')
	{
		$this->formItem->rules(['chsAlpha' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证是否是汉字、字母和数字
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function chsAlphaNumRule($text = '[字段]不是只能是汉字、字母和数字')
	{
		$this->formItem->rules(['chsAlphaNum' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证是否是汉字、字母、数字和下划线_及破折号-
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function chsDashRule($text = '[字段]不是汉字、字母、数字和下划线_及破折号-')
	{
		$this->formItem->rules(['chsDash' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证是否是小写字符
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function lowerRule($text = '[字段]不是是小写字符')
	{
		$this->formItem->rules(['lower' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证是否是大写字符
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function upperRule($text = '[字段]不是是大写字符')
	{
		$this->formItem->rules(['upper' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证输入的值是否在范围内
	 * @param string $str 范围
	 * @param string $text 文案，必须带上[字段]、[范围]
	 * @return $this
	 */
	public function inRule($str = '1,2,3', $text = '[字段]在[范围]内')
	{
		$this->formItem->rules(["in:{$str}" => str_replace([
			'[字段]',
			'[范围]',
		], [
			$this->formItem->attr('label'),
			$str,
		], $text)]);
		return $this;
	}

	/**
	 * 验证输入的值是否不在范围内
	 * @param string $str 范围
	 * @param string $text 文案，必须带上[字段]、[范围]
	 * @return $this
	 */
	public function notInRule($str = '1,2,3', $text = '[字段]在[范围]内')
	{
		$this->formItem->rules(["notIn:{$str}" => str_replace([
			'[字段]',
			'[范围]',
		], [
			$this->formItem->attr('label'),
			$str,
		], $text)]);
		return $this;
	}
}