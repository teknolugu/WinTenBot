<?php
/**
 * Created by IntelliJ IDEA.
 * User: azhe403
 * Date: 19/01/19
 * Time: 0:35
 */

namespace src\Model;

use GuzzleHttp\Client;

class Notes
{
	/**
	 * @param $chat_id
	 * @return \Psr\Http\Message\StreamInterface
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public static function getNotes($chat_id)
	{
		$client = new Client(['base_uri' => new_api]);
		$response = $client->request('GET', '/notes', [
			'query'   => [
				'id_chat' => $chat_id,
			],
			'headers' => [
				'token' => new_token,
			],
		]);
		
		return $response->getBody();
	}
	
}
