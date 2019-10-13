<?php
/**
 * Created by IntelliJ IDEA.
 * User: Azhe
 * Date: 1/18/2019
 * Time: 6:54 AM
 */

namespace WinTenDev\Model;

use Medoo\Medoo;

class Spell
{
	/**
	 * @param $datas
	 * @return bool|\PDOStatement
	 */
	public static function addSpell($datas)
	{
		$db = new Medoo(db_data);
		return $db->insert('spells', $datas);
	}
	
	/**
	 * @return array|bool
	 */
	public static function spellText($text)
	{
		$db = new Medoo(db_data);
		$d = $db->select('spells', '*');
		foreach ($d as $e) {
			$text = str_replace($e['typo'], $e['fix'], $text);
		}
		return $text;
	}
	
	/**
	 * @return array|bool
	 */
	public static function listSpell()
	{
		$db = new Medoo(db_data);
		return $db->select('spells', '*');
	}
	
	public static function canInsert()
	{
		//
	}
}
