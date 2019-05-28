<?php

namespace src\Model;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\StreamInterface;

class Cas
{
	/**
	 * @param $user_id
	 * @return bool|StreamInterface
	 * @throws GuzzleException
	 */
	public static function checkCas($user_id)
	{
		$isBanned = false;
		$baseUrl = 'https://combot.org/api/cas/check';
		$client = new Client(['base_uri' => $baseUrl]);
		$response = $client->request('GET', '/tags', [
			'query' => [
				'id_chat' => $user_id,
			],
		]);
		
		$json = \GuzzleHttp\json_decode($response->getBody(), true);
		if ($json['ok'] == 'true') {
			$isBanned = true;
		}
		return $isBanned;
	}
}
