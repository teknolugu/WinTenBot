<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 1/20/2019
 * Time: 9:40 PM
 */

namespace src\Model;

use GuzzleHttp\Client;
use Medoo\Medoo;
use src\Utils\DatabaseProvider;

class Settings
{
	
	/**
	 * @param $data
	 * @return \Psr\Http\Message\StreamInterface
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public static function save($data)
	{
//        $db = new Medoo(db_data);
//        return self::$DB->update('settings', ['test' => 'wik'], ['chat_id' => '-1001387872546']);
		$client = new Client(['base_uri' => new_api]);
		$response = $client->request('POST', '/group-settings', [
			'headers'     => [
				'Accept' => 'application/json',
				'token'  => new_token,
			],
			'form_params' => $data,
		
		]);
		
		return $response->getBody();
	}
	
	/**
	 * @param array $datas
	 * @param array $where
	 * @return bool|\PDOStatement
	 */
	public static function saveNew(array $datas, array $where)
	{
		$db = new Medoo(db_data);
		$table = 'group_settings';
		$p = $db->count($table, ['chat_id' => $datas['chat_id']]);
		if ($p > 0) {
			$q = $db->update($table, $datas, $where);
		} else {
			$q = $db->insert($table, $datas);
		}
		
		return $q;
	}
	
	public static function getNew($where)
	{
		$db = new Medoo(db_data);
		return $db->select('group_settings', '*', $where);
	}
	
	/**
	 * @param $data
	 * @return \Psr\Http\Message\StreamInterface
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public static function get($data)
	{
		$client = new Client(['base_uri' => new_api]);
		$response = $client->request('GET', '/group-settings', [
			'headers' => [
				'Accept' => 'application/json',
				'token'  => new_token,
			],
			'query'   => $data,
		
		]);
		
		return $response->getBody();
	}
}
