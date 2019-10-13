<?php

namespace WinTenDev\Model;

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
		$url = "https://combot.org/api/cas/check?user_id=$user_id";
//		$baseUrl = 'https://combot.org/api';
//		$client = new Client(['base_uri' => $baseUrl]);
//		$response = $client->request('GET', '/cas/check', [
//			'query' => [
//				'user_id' => $user_id,
//			],
//		]);
		
		$json = \GuzzleHttp\json_decode($url, true);
		if ($json['ok'] == 1) {
			$isBanned = true;
		}
		return $isBanned;
	}
}
