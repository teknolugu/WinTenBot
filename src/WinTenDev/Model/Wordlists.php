<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 2/23/2019
 * Time: 10:28 AM
 */

namespace WinTenDev\Model;

use Medoo\Medoo;
use PDOStatement;
use WinTenDev\Utils\Caches;
use WinTenDev\Utils\Words;

class Wordlists
{
	public static function addWords($datas)
	{
		$db = new Medoo(db_data);
		$table = 'wordlists';
		$p = $db->count($table, ['word' => $datas['word']]);
		if ($p > 0) {
			$q = $db->update($table, $datas, ['word' => $datas['word']]);
		} else {
			$q = $db->insert($table, $datas);
		}
		
		return $q;
	}
	
	/**
	 * @param $where
	 * @return bool|PDOStatement
	 */
	public static function delTags($where)
	{
		$db = new Medoo(db_data);
		return $db->delete('wordlists', $where);
	}
	
	/**
	 * @return mixed
	 */
	public static function loadFromFile()
	{
		$filePath = botData . 'wordlists.json';
		return \GuzzleHttp\json_decode(file_get_contents($filePath), true);
	}
	
	/**
	 * @return bool|int
	 */
	public static function writeCache()
	{
		$datas = self::getAll();
		$cache = new Caches();
		return $cache->writeCache('cache-json', 'wordlist', $datas);
	}
	
	/**
	 * @return array|bool
	 */
	public static function getAll()
	{
		$db = new Medoo(db_data);
		return $db->select('wordlists', [
			'word', 'class',
		], [
			'ORDER' => 'word',
		]);
	}
	
	/**
	 * @param $pesan
	 * @return bool
	 */
	public static function isContainBadword($pesan): bool
	{
//        $wordlists = self::getAll();
//        $wordlists = self::loadFromFile();
		$isBadword = false;
		$wordlists = self::readCache();
		if (!is_array($wordlists)) {
			return false;
		}
		
		if (count($wordlists) > 0) {
//        $apesan = explode(' ', $pesan);
			$apesan = Words::multiexplode([' ', "\n"], $pesan);
			foreach ($apesan as $anu) {
				foreach ($wordlists as $kata) {
					if (Words::isSameWith($anu, $kata['word'])) {
						$isBadword = true;
					}
				}
			}
		}
		
		return $isBadword;
	}
	
	/**
	 * @return mixed
	 */
	public static function readCache()
	{
		$cache = new Caches();
		return $cache->readCache('cache-json', 'wordlist');
	}
}
