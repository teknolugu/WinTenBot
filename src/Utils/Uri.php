<?php

namespace src\Utils;

class Uri
{
	public static function validate_url($url)
	{
		$path = parse_url($url, PHP_URL_PATH);
		$encoded_path = array_map('urlencode', explode('/', $path));
		$url = str_replace($path, implode('/', $encoded_path), $url);
		
		return filter_var($url, FILTER_VALIDATE_URL) ? true : false;
	}
	
	public static function is_url($url)
	{
		$response = [];
		//Check if URL is empty
		if (!empty($url)) {
			$response = get_headers($url);
		}
		return (bool)in_array('HTTP/1.1 200 OK', $response, true);
		/*Array
		(
			[0] => HTTP/1.1 200 OK
			[Date] => Sat, 29 May 2004 12:28:14 GMT
			[Server] => Apache/1.3.27 (Unix)  (Red-Hat/Linux)
			[Last-Modified] => Wed, 08 Jan 2003 23:11:55 GMT
			[ETag] => "3f80f-1b6-3e1cb03b"
			[Accept-Ranges] => bytes
			[Content-Length] => 438
			[Connection] => close
			[Content-Type] => text/html
		)*/
	}
}
