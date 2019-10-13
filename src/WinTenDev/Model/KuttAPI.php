<?php
/**
 * Created by IntelliJ IDEA.
 * User: Azhe
 * Date: 12/30/2018
 * Time: 11:56 AM
 */

namespace WinTenDev\Model;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class KuttAPI
{
	static $base_url = "https://kutt.it/api/";
	
	public static function submit($data)
	{
		$headers = [
			'X-API-Key'    => kutt_token,
			'Content-Type' => 'application/x-www-form-urlencoded',
		];
		
		$postUrl = "https://kutt.it/api/url/submit";
//        $client = new Client(['base_uri' => self::$base_url]);
		$response = new Request("POST", $postUrl, $headers, $data);
		return $response->getBody();
	}
	
	public static function tambahTag($datas)
	{
		$uri = "https://kutt.it/api/url/submit";
		$client = new Client();
		$response = $client->request('POST', $uri, [
			'form_params' => $datas,
		]);
		
		return $response->getBody();
	}
}
