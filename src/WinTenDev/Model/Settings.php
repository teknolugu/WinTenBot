<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 1/20/2019
 * Time: 9:40 PM
 */

namespace WinTenDev\Model;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Medoo\Medoo;
use Psr\Http\Message\StreamInterface;
use WinTenDev\Utils\Converters;
use WinTenDev\Utils\Caches;

class Settings
{

//	/**
//	 * @param $data
//	 * @return \Psr\Http\Message\StreamInterface
//	 * @throws \GuzzleHttp\Exception\GuzzleException
//	 */
//	public static function save($data)
//	{
////        $db = new Medoo(db_data);
////        return self::$DB->update('settings', ['test' => 'wik'], ['chat_id' => '-1001387872546']);
//		$client = new Client(['base_uri' => new_api]);
//		$response = $client->request('POST', '/group-settings', [
//			'headers'     => [
//				'Accept' => 'application/json',
//				'token'  => new_token,
//			],
//			'form_params' => $data,
//
//		]);
//
//		return $response->getBody();
//	}
	
	/**
	 * @param array $datas
	 * @param array $where
	 * @return bool|PDOStatement
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
	
	/**
	 * @param array $datas
	 * @return bool|PDOStatement
	 */
	public static function save(array $datas)
	{
		$db = new Medoo(db_data);
		$table = 'group_settings';
		$where = [
			'chat_id' => $datas['chat_id'],
		];
		
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
	 * @param string $chat_id
	 * @param bool   $isPrivate
	 * @return array|bool
	 */
	public static function getForTombol(string $chat_id, bool $isPrivate = false): array
	{
		$db = new Medoo(db_data);
		
		$forButton = [
			'enable_badword_filter',
			'enable_url_filtering',
		];
		
		if ($isPrivate) {
			$forButton = array_merge($forButton,
				[
					'enable_reply_notification',
				]);
		} else {
			$forButton = array_merge($forButton,
				[
					'enable_federation_ban',
					'enable_unified_welcome',
					'enable_warn_username',
					'enable_welcome_message',
					'enable_human_verification',
					'enable_badword_filter',
					'enable_url_filtering',
				]);
		}
		
		return $db->select('group_settings', $forButton, ['chat_id' => $chat_id]);
	}
	
	/**
	 * @param $data
	 * @return void
	 */
	public static function inlineSetting($data)
	{
		$chat_id = $data['chat_id'];
		$inline = $data['inline'];
		$pecah = explode('_', $inline); // enable_bot || disable_bot
		$col = 'enable' . ltrim($inline, $pecah[0]); // enable_bot
		$int = Converters::stringToInt($pecah[0]);
		self::saveNew([
			$col      => $int,
			'chat_id' => $chat_id,
		], [
			'chat_id' => $chat_id,
		]);
	}
	
	/**
	 * @param $data
	 * @return void
	 */
	public static function toggleSetting($data)
	{
		$chat_id = $data['chat_id'];
		$toggle = $data['toggle'];
		$where = ['chat_id' => $chat_id];
		$curr = self::getForTombol($chat_id);
		$set = $curr[0][$toggle] == 1 ? 0 : 1;
		
		
		self::saveNew([
			$toggle   => $set,
			'chat_id' => $chat_id,
		], $where);
	}
	
	/**
	 * @param string $chat_id
	 * @param bool   $isHard
	 * @return bool|PDOStatement
	 */
	public static function softReset(string $chat_id, bool $isHard = false)
	{
		$db = new Medoo(db_data);
		$table = 'group_settings';
		
		if ($isHard) {
			$datas = [
				'chat_id'                          => $chat_id,
				'enable_bot'                       => 1,
				'enable_anti_malfiles'             => 1,
				'enable_badword_filter'            => 1,
				'enable_url_filtering'             => 1,
				'enable_human_verification'        => 0,
				'enable_federation_ban'            => 1,
				'enable_unified_welcome'           => 1,
				'enable_warn_username'             => 0,
				'last_setting_message_id'          => -1,
				'last_tags_message_id'             => -1,
				'last_welcome_message_id'          => -1,
				'last_warning_username_message_id' => -1,
				'warning_username_limit'           => 7,
			];
		} else {
			$datas = [
				'chat_id'                          => $chat_id,
				'last_setting_message_id'          => -1,
				'last_tags_message_id'             => -1,
				'last_welcome_message_id'          => -1,
				'last_warning_username_message_id' => -1,
			];
		}
		
		$p = $db->count($table, ['chat_id' => $datas['chat_id']]);
		if ($p > 0) {
			$q = $db->update($table, $datas, ['chat_id' => $datas['chat_id']]);
		} else {
			$q = $db->insert($table, $datas);
		}
		
		return $q;
	}
	
	/**
	 * @param $chat_id
	 * @param $datas
	 */
	public static function writeCache($chat_id, $datas)
	{
		$cache = new Caches();
		$cache->writeCache("cache-json/{$chat_id}", 'settings', $datas);
	}
	
	/**
	 * @param $chat_id
	 * @return mixed
	 */
	public static function readCache($chat_id)
	{
		$cache = new Caches();
		return $cache->readCache("cache-json/{$chat_id}", 'settings');
	}
	
	/**
	 * @param $data
	 * @return StreamInterface
	 * @throws GuzzleException
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
