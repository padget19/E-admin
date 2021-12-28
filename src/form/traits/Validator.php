<?php


namespace Eadmin\form\traits;


trait Validator
{
	/**
	 * 验证最多字符
	 * @param int    $num  数量
	 * @param string $text 文案，必须带上[字段]、[数量]
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
	 * @param int    $num  字符
	 * @param string $text 文案，必须带上[字段]、[数量]
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
	 * 验证某个字段的值只能是空白字符（包括缩进，垂直制表符，换行符，回车和换页字符）
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function spaceRule($text = '[字段]不是空白字符（包括缩进，垂直制表符，换行符，回车和换页字符）')
	{
		$this->formItem->rules(['space' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证某个字段的值是否在某个日期之后
	 * @param string $date 日期
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function afterRule($date, $text = '[字段]不在[日期]后')
	{
		$this->formItem->rules([
			"after:{$date}" => str_replace(
				[
					'[字段]',
					'[日期]',
				], [
					$this->formItem->attr('label'),
					$date,
				]
				, $text),
		]);
		return $this;
	}

	/**
	 * 验证某个字段的值是否在某个日期之前
	 * @param string $date 日期
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function beforeRule($date, $text = '[字段]不在[日期]前')
	{
		$this->formItem->rules([
			"before:{$date}" => str_replace(
				[
					'[字段]',
					'[日期]',
				], [
					$this->formItem->attr('label'),
					$date,
				]
				, $text),
		]);
		return $this;
	}

	/**
	 * 验证当前操作（注意不是某个值）是否在某个有效日期之内
	 * @param string $startDate 开始日期
	 * @param string $endDate   结束日期
	 * @param string $text      文案，必须带上[字段]
	 * @return $this
	 */
	public function expireRule($startDate, $endDate, $text = '[字段]不在[开始] ~ [结束]内')
	{
		$this->formItem->rules([
			"expire:{$startDate},{$endDate}" => str_replace(
				[
					'[字段]',
					'[开始]',
					'[结束]',
				], [
					$this->formItem->attr('label'),
					$startDate,
					$endDate,
				]
				, $text),
		]);
		return $this;
	}

	/**
	 * 验证当前请求的IP是否在某个范围，多个IP用逗号分隔
	 * @param string $ip
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function allowIpRule($ip, $text = '[字段]请求的IP不在允许范围内')
	{
		$this->formItem->rules(["allowIp:{$ip}" => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证当前请求的IP是否禁止访问，多个IP用逗号分隔
	 * @param string $ip
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function denyIpRule($ip, $text = '[字段]请求的IP已被禁止访问')
	{
		$this->formItem->rules(["denyIp:{$ip}" => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证是否等于某个值
	 * @param int    $num
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function eqRule($num, $text = '[字段]不等于[值]')
	{
		$this->formItem->rules([
			"eq:{$num}" => str_replace([
				'[字段]',
				'[值]',
			], [
				$this->formItem->attr('label'),
						   $num,
			], $text),
		]);
		return $this;
	}

	/**
	 * 验证是否大于某个值
	 * @param int    $num
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function gtRule($num, $text = '[字段]不大于[值]')
	{
		$this->formItem->rules([
			"gt:{$num}" => str_replace([
				'[字段]',
				'[值]',
			], [
				$this->formItem->attr('label'),
				$num,
			], $text),
		]);
		return $this;
	}

	/**
	 * 验证是否大于等于某个值
	 * @param int    $num
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function egtRule($num, $text = '[字段]不大于等于[值]')
	{
		$this->formItem->rules([
			"egt:{$num}" => str_replace([
				'[字段]',
				'[值]',
			], [
				$this->formItem->attr('label'),
				$num,
			], $text),
		]);
		return $this;
	}

	/**
	 * 验证是否小于等于某个值
	 * @param int    $num
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function eltRule($num, $text = '[字段]不小于等于[值]')
	{
		$this->formItem->rules([
			"elt:{$num}" => str_replace([
				'[字段]',
				'[值]',
			], [
				$this->formItem->attr('label'),
				$num,
			], $text),
		]);
		return $this;
	}

	/**
	 * 验证是否小于某个值
	 * @param int    $num
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function ltRule($num, $text = '[字段]不小于[值]')
	{
		$this->formItem->rules([
			"lt:{$num}" => str_replace([
				'[字段]',
				'[值]',
			], [
				$this->formItem->attr('label'),
				$num,
			], $text),
		]);
		return $this;
	}

	/**
	 * 验证某个字段的值只能是十六进制字符串
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function xdigitRule($text = '[字段]不是十六进制字符串')
	{
		$this->formItem->rules(['xdigit' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证某个字段的值是否为有效的域名或者IP
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function activeUrlRule($text = '[字段]不是有效的域名或者IP')
	{
		$this->formItem->rules(['activeUrl' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证某个字段的值是否为有效的IP地址（采用filter_var验证）
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function ipRule($text = '[字段]不是有效的IP')
	{
		$this->formItem->rules(['ip' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证某个字段的值是否为指定格式的日期
	 * @param string $rule 格式化类型
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function dateFormatRule($rule = 'Y-m-d', $text = '[字段]不是指定格式的日期')
	{
		$this->formItem->rules(["dateFormat:{$rule}" => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证某个字段的值是否为纯数字（采用ctype_digit验证，不包含负数和小数点）
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function numRule($text = '[字段]不是纯数字')
	{
		$this->formItem->rules(['number' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证某个字段的值是否为整数（采用filter_var验证）
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function intRule($text = '[字段]不是整数')
	{
		$this->formItem->rules(['integer' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证某个字段的值是否为浮点数字（采用filter_var验证）
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function floatRule($text = '[字段]不是浮点数字')
	{
		$this->formItem->rules(['float' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证某个字段的值是否为布尔值（采用filter_var验证）
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function boolRule($text = '[字段]不是布尔值')
	{
		$this->formItem->rules(['boolean' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证某个字段的值是否为数组
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function arrayRule($text = '[字段]不是数组')
	{
		$this->formItem->rules(['array' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证某个字段是否为为 yes, on, 或是 1。这在确认"服务条款"是否同意时很有用
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function acceptedRule($text = '[字段]不为真')
	{
		$this->formItem->rules(['accepted' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证值是否为有效的日期
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function dateRule($text = '[字段]不为真')
	{
		$this->formItem->rules(['date' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
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
	 * 验证某个字段的值是否为有效的MAC地址
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function macAddrRule($text = '[字段]不是有效的MAC地址')
	{
		$this->formItem->rules(['macAddr' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证某个字段的值是否为有效的邮政编码
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function zipRule($text = '[字段]不是有效的邮政编码')
	{
		$this->formItem->rules(['zip' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
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
				$this->formItem->attr('label'),
			], $text),
		]);
		return $this;
	}

	/**
	 * 验证某个字段的值的长度是否在某个范围
	 * @param string $rule 开始值
	 * @param string $text 文案
	 * @return $this
	 */
	public function lengthRule($rule, $text = '[字段]在[范围]之间')
	{
		$this->formItem->rules([
			"length:{$rule}" => str_replace([
				'[字段]',
				'[范围]',
			], [
				$rule,
				$this->formItem->attr('label'),
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
				$this->formItem->attr('label'),
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
	 * 验证某个字段的值只能是控制字符（换行、缩进、空格）
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function cntrlRule($text = '[字段]不是控制字符（换行、缩进、空格）')
	{
		$this->formItem->rules(['cntrl' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证某个字段的值只能是可打印字符（空格除外）
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function graphRule($text = '[字段]不是可打印字符（空格除外）')
	{
		$this->formItem->rules(['graph' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
		return $this;
	}

	/**
	 * 验证某个字段的值只能是可打印字符（包括空格）
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function printRule($text = '[字段]不是可打印字符（包括空格）')
	{
		$this->formItem->rules(['print' => str_replace('[字段]', $this->formItem->attr('label'), $text)]);
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
	 * @param string $str  范围
	 * @param string $text 文案，必须带上[字段]、[范围]
	 * @return $this
	 */
	public function inRule($str = '1,2,3', $text = '[字段]在[范围]内')
	{
		$this->formItem->rules([
			"in:{$str}" => str_replace([
				'[字段]',
				'[范围]',
			], [
				$this->formItem->attr('label'),
				$str,
			], $text),
		]);
		return $this;
	}

	/**
	 * 验证输入的值是否不在范围内
	 * @param string $str  范围
	 * @param string $text 文案，必须带上[字段]、[范围]
	 * @return $this
	 */
	public function notInRule($str = '1,2,3', $text = '[字段]在[范围]内')
	{
		$this->formItem->rules([
			"notIn:{$str}" => str_replace([
				'[字段]',
				'[范围]',
			], [
				$this->formItem->attr('label'),
				$str,
			], $text),
		]);
		return $this;
	}
}