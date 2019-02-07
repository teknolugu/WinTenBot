<?php
/**
 * Created by IntelliJ IDEA.
 * User: azhe403
 * Date: 19/01/19
 * Time: 0:35
 */

namespace src\Model;

use GuzzleHttp\Client;

class Tags
{
	/**
	 * @param $chat_id
	 * @return \Psr\Http\Message\StreamInterface
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public static function getTags($chat_id)
	{
		$client = new Client(['base_uri' => new_api]);
		$response = $client->request('GET', '/tags', [
			'query'   => [
				'id_chat' => $chat_id,
			],
			'headers' => [
				'token' => new_token,
			],
		]);
		
		return $response->getBody();
	}
	
	/**
	 * @param $datas
	 * @return \Psr\Http\Message\StreamInterface
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public static function selectTags($datas)
	{
		$client = new Client(['base_uri' => new_api]);
		$response = $client->request('GET', '/tags/select', [
			'headers' => [
				'token' => new_token,
			],
			'query'   => $datas,
		]);
		
		return $response->getBody();
	}
	
	/**
	 * @param $datas
	 * @return \Psr\Http\Message\StreamInterface
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public static function addTags($datas)
	{
		$client = new Client(['base_uri' => new_api]);
		$response = $client->request('POST', '/tags', [
			'headers'     => [
				'token' => new_token,
			],
			'form_params' => $datas,
		]);
		
		return $response->getBody();
	}
	
	/**
	 * @param $datas
	 * @return \Psr\Http\Message\StreamInterface
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public static function deleteTags($datas)
	{
		$client = new Client(['base_uri' => new_api]);
		$response = $client->request('DELETE', '/tags', [
			'headers'     => [
				'token' => new_token,
			],
			'form_params' => $datas,
		]);
		
		return $response->getBody();
	}
}
