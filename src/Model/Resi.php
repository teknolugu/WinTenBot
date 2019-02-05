<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 1/20/2019
 * Time: 10:38 AM
 */

namespace src\Model;

class Resi
{
	/**
	 * @param $kurir
	 * @param $resi
	 * @return string
	 */
	public static function cekResi($kurir, $resi)
	{
		$url = new_api . "/resi?kurir=$kurir&resi=$resi";
		return file_get_contents($url);
	}
}
