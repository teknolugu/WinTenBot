<?php
/**
 * Created by IntelliJ IDEA.
 * User: azhe403
 * Date: 19/01/19
 * Time: 0:35
 */

namespace src\Model;

use Medoo\Medoo;
use PDOStatement;

class Tags
{
//	/**
//	 * @param $chat_id
//	 * @return \Psr\Http\Message\StreamInterface
//	 * @throws \GuzzleHttp\Exception\GuzzleException
//	 */
//	public static function getTags($chat_id)
//	{
//		$client = new Client(['base_uri' => new_api]);
//		$response = $client->request('GET', '/tags', [
//			'query'   => [
//				'id_chat' => $chat_id,
//			],
//			'headers' => [
//				'token' => new_token,
//			],
//		]);
//
//		return $response->getBody();
//	}
//
//	/**
//	 * @param $datas
//	 * @return \Psr\Http\Message\StreamInterface
//	 * @throws \GuzzleHttp\Exception\GuzzleException
//	 */
//	public static function selectTags($datas)
//	{
//		$client = new Client(['base_uri' => new_api]);
//		$response = $client->request('GET', '/tags/select', [
//			'headers' => [
//				'token' => new_token,
//			],
//			'query'   => $datas,
//		]);
//
//		return $response->getBody();
//	}
//
//	/**
//	 * @param $datas
//	 * @return \Psr\Http\Message\StreamInterface
//	 * @throws \GuzzleHttp\Exception\GuzzleException
//	 */
//	public static function addTags($datas)
//	{
//		$client = new Client(['base_uri' => new_api]);
//		$response = $client->request('POST', '/tags', [
//			'headers'     => [
//				'token' => new_token,
//			],
//			'form_params' => $datas,
//		]);
//
//		return $response->getBody();
//	}
//
//	/**
//	 * @param $datas
//	 * @return \Psr\Http\Message\StreamInterface
//	 * @throws \GuzzleHttp\Exception\GuzzleException
//	 */
//	public static function deleteTags($datas)
//	{
//		$client = new Client(['base_uri' => new_api]);
//		$response = $client->request('DELETE', '/tags', [
//			'headers'     => [
//				'token' => new_token,
//			],
//			'form_params' => $datas,
//		]);
//
//		return $response->getBody();
//	}
	
	/**
	 * @param array $datas
	 * @return bool|PDOStatement
	 */
	public static function saveTag(array $datas)
	{
		$db = new Medoo(db_data);
		$table = 'tags';
		$where = [
			'tag'     => $datas['tag'],
			'id_chat' => $datas['id_chat'],
		];
		
		$p = $db->count($table, $where);
		if ($p > 0) {
			$q = $db->update($table, $datas, $where);
		} else {
			$q = $db->insert($table, $datas);
		}
		
		return $q;
	}
	
	/**
	 * @param $chat_id
	 * @return array|bool
	 */
	public static function getTags($chat_id)
	{
		$db = new Medoo(db_data);
		//		self::writeCache($chat_id, $datas);
		return $db->select('tags', '*', ['id_chat' => $chat_id, 'ORDER' => 'tag']);
	}
	
	public static function getTag($where)
	{
		$db = new Medoo(db_data);
		//		self::writeCache($chat_id, $datas);
		return $db->select('tags', '*', $where);
	}
	
	public static function delTags($where)
	{
		$db = new Medoo(db_data);
		return $db->delete('tags', $where);
	}
	
	/**
	 * @param $where
	 * @return array|bool
	 */
	public static function getAll($where)
	{
		$db = new Medoo(db_data);
		return $db->select('tags', '*');
	}
	
	public static function clearTag($text)
	{
		$text = str_replace(
			['#', '-'],
			['', '_'],
			$text);
		return $text;
	}
	
	public static function writeCache($chat_id, $datas)
	{
		$cache = new Caches();
		$cache->writeCache("cache-json/{$chat_id}", 'tags', $datas);
	}
	
	public static function readCache($chat_id)
	{
		$cache = new Caches();
		return $cache->readCache("cache-json/{$chat_id}", 'tags');
	}
}
