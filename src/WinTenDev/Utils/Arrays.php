<?php

namespace WinTenDev\Utils;

class Arrays
{
	/**
	 * @param  $array
	 * @param  $condition
	 * @return array
	 */
	public static function arrayFilter(array $array, array $condition): array
	{
		$foundItems = [];
		
		foreach ($array as $item) {
			$find = true;
			foreach ($condition as $key => $value) {
				if (isset($item[$key]) && $item[$key] == $value) {
					$find = true;
				} else {
					$find = false;
				}
			}
			if ($find) {
				$foundItems[] = $item;
			}
		}
		return $foundItems;
	}
	
	public static function toJson($data)
	{
		header('Content-Type: application/json');
		echo \GuzzleHttp\json_encode($data, JSON_PRETTY_PRINT);
	}
}
