<?php
/**
 * Author: Ivo Toman
 */

namespace Pohoda;


class Validators
{

	public static function assertBoolean($value)
	{
		if (self::isBoolean($value) === false) {
			throw new \InvalidArgumentException("value ($value) is not numeric");
		}
	}


	public static function isBoolean($value)
	{
		return is_bool($value);
	}


	public static function assertDate($value)
	{
		if (self::isDate($value) === false) {
			throw new \InvalidArgumentException("value ($value) is not a date");
		}
	}


	public static function isDate($value)
	{
		return (($value instanceof \DateTime) || date_create($value));
	}


	public static function assertNumeric($value)
	{
		if (self::isNumeric($value) === false) {
			throw new \InvalidArgumentException("value ($value) is not numeric");
		}
	}


	public static function isNumeric($value)
	{
		return is_numeric($value);
	}


	public static function assertMaxLength($value, $maxLength)
	{
		if (self::isMaxLength($value, $maxLength) === false) {
			throw new \InvalidArgumentException("value ($value) is longer than $maxLength");
		}
	}


	public static function isMaxLength($value, $maxLength)
	{
		return (mb_strlen($value ?? '') <= $maxLength);
	}


	public static function assertKeyInList($value, array $list)
	{
		if (self::isKeyInList($value, $list) === false) {
			throw new \InvalidArgumentException("value ($value) is not in " . explode(",", (string)array_keys($list)));
		}
	}


	public static function isKeyInList($value, array $list)
	{
		return array_key_exists($value, $list);
	}

}
