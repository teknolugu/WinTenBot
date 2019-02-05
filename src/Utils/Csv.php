<?php
/**
 * Created by IntelliJ IDEA.
 * User: Azhe
 * Date: 1/3/2019
 * Time: 9:54 PM
 */

namespace src\Utils;

class Csv
{
	public static function tulis($data)
	{
		$f = fopen('../Data/spell.csv', 'w');
		fputcsv($f, $data);
		fclose($f);
	}
}
