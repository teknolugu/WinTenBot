<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 2/22/2019
 * Time: 4:46 PM
 */

namespace src\Utils;

class Converters
{
	/**
	 * @param $int
	 * @return string
	 */
	public static function intToEmoji($int)
	{
		return $int == 1 ? '✅' : '❌';
	}
	
	/**
	 * @param $string
	 * @return integer
	 */
	public static function stringToInt($string)
	{
		return $string == 'on' ? '1' : '0';
	}
}
