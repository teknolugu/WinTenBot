<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 17.21
 */

namespace WinTenDev\Model;

use ErrorException;
use Exception;
use Stichoza\GoogleTranslate\GoogleTranslate;

class Translator
{
	/**
	 * @param $text
	 * @param $from
	 * @param $to
	 * @return array
	 * @throws Exception
	 */
	public static function Exe($text, $from, $to)
	{
		$tr = new GoogleTranslate(); // Default is from 'auto' to 'en'
		$tr->setOptions( [
			'timeout' => 10,
			'proxy' => [
				'http' => 'tcp://0.0.0.0:8080',
//				'http' => 'tcp://0.0.0.0:3123'//..add more if you wish
			],
			'headers' => [
				'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36'
			]
		]);
		
		if ($to !== null) {
			$tr->setTarget($to); // Translate to
		} else {
			$tr->setSource();
			$tr->setTarget($from);
			$to = $from;
			$translated = $tr->translate($text);
			$from = $tr->getLastDetectedSource();
		}
		
		return [
			'from' => $from,
			'to'   => $to,
			'text' => $translated,
		];
	}
	
	/**
	 * @param $text
	 * @param $langId
	 * @return string|null
	 * @throws ErrorException
	 */
	public static function To($text, $langId)
	{
//		$res = self::Exe($text, '', $langId);
		$tr = new GoogleTranslate();
		return $tr->setSource(null)->setTarget($langId)->translate($text);
	}
}
