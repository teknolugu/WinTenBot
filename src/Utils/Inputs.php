<?php

namespace src\Utils;

class Inputs
{
	/**
	 * @param string $param
	 * @return mixed
	 */
	public static function get(string $param)
	{
		/** @var array $_GET */
		$get = $_GET;
		
		if ($get != null) {
			return $get[$param];
		} else {
			return null;
		}
	}
	
	/**
	 * @param string $param
	 * @return mixed|null
	 */
	public static function globals(string $param)
	{
		$globals = $GLOBALS;
		
		if ($globals != null) {
			return $globals[$param];
		} else {
			return null;
		}
	}
}
