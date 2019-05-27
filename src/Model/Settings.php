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
use src\Utils\Converters;
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
	
	public static function getForTombol($chat_id)
	{
		$db = new Medoo(db_data);
		return $db->select('group_settings', [
			'enable_badword_filter',
			'enable_federation_ban',
			'enable_human_verification',
			'enable_unified_welcome',
			'enable_url_filtering',
			'enable_warn_username',
		], ['chat_id' => $chat_id]);
	}
	
	public static function inlineSetting($data)
	{
		$chat_id = $data['chat_id'];
		$inline = $data['inline'];
		$pecah = explode('_', $inline); // enable_bot || disable_bot
		$col = 'enable' . ltrim($inline, $pecah[0]); // enable_bot
		$int = Converters::stringToInt($pecah[0]);
		return self::saveNew([
			$col      => $int,
			'chat_id' => $chat_id,
		], [
			'chat_id' => $chat_id,
		]);
	}
	
	public static function toggleSetting($data)
	{
		$chat_id = $data['chat_id'];
		$toggle = $data['toggle'];
		$where = ['chat_id' => $chat_id];
		$curr = self::getForTombol($where);
		$set = $curr[0][$toggle] == 1 ? 0 : 1;
		
		self::saveNew([
			$toggle   => $set,
			'chat_id' => $chat_id,
		], $where);
	}
	
	/**
	 * @param      $chat_id
	 * @param bool $isHard
	 * @return bool|\PDOStatement
	 */
	public static function softReset($chat_id, $isHard = false)
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
