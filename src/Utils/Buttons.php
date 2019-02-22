<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 2/19/2019
 * Time: 8:07 PM
 */

namespace src\Utils;

class Buttons
{
	/**
	 * @param string $btn_data
	 * @return array
	 */
	public static function Generate($btn_data)
	{
		$btn_markup = [];
		$btn_datas = explode(',', $btn_data);
		foreach ($btn_datas as $key => $val) {
			$btn_row = explode('|', $val);
			$btn_markup[] = ['text' => $btn_row[0], 'url' => $btn_row[1]];
		}
		
		return $btn_markup;
	}
}
