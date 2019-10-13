<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 2/10/2019
 * Time: 9:35 AM
 */

namespace WinTenDev\Model;

use Medoo\Medoo;

class DB
{
	/**
	 * @param $table
	 * @param $datas
	 * @return bool|\PDOStatement
	 */
	public static function insert($table, $datas)
	{
		$db = new Medoo(db_data);
		return $db->insert($table, $datas);
	}
	
	/**
	 * @param $table
	 * @param $datas
	 * @param $where
	 * @return array|bool
	 */
	public static function insertOrUpdate($table, $datas, $where)
	{
		$db = new Medoo(db_data);
		$p = $db->count($table, $where);
		if ($p > 0) {
			$q = $db->update($table, $datas, $where);
		} else {
			$q = $db->insert($table, $datas);
		}
		
		return $q;
	}
}
