<?php
/**
 * Created by IntelliJ IDEA.
 * User: Azhe
 * Date: 1/18/2019
 * Time: 6:54 AM
 */

namespace src\Model;

use GuzzleHttp\Client;

class Spell
{
	protected static $DB;
	protected static $table;
	
	public static function addSpell($datas)
	{
		self::$client = new Client(['base_uri' => new_api]);
		$response = self::$client->request('POST', '/spell', [
			'headers'     => [
				'token' => new_token,
			],
			'form_params' => $datas,
		]);
		
		return $response->getBody();
	}
	
	/**
	 * @param $text
	 * @return \Psr\Http\Message\StreamInterface
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public static function fixTypo($text)
	{
		$client = new Client(['base_uri' => new_api]);
		$response = $client->request('GET', '/spell', [
			'query' => [
				'text' => $text,
			],
		]);
		
		return $response->getBody();
	}
	
	/**
	 * @param $text
	 * @return false|string
	 */
	public static function typo($text)
	{
		$url = new_api . "/spell?text=$text";
		return file_get_contents($url);
	}
}
