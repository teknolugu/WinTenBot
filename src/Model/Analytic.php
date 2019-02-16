<?php
/**
 * Created by IntelliJ IDEA.
 * User: Azhe
 * Date: 12/16/2018
 * Time: 3:37 PM
 */

namespace src\Model;

use Medoo\Medoo;

class Analytic
{
	
	/**
	 * @param $datas
	 */
	public static function logChat($datas)
	{
		$db = new Medoo(db_data);
		$db->insert('subscribers', $datas);
		
	}
}
