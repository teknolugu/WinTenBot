<?php
/**
 * Created by IntelliJ IDEA.
 * User: Azhe
 * Date: 12/16/2018
 * Time: 3:40 PM
 */

namespace src\Model;

class Bot
{
	/**
	 * @param $data
	 * @return false|string
	 */
	public static function getTermsUse($data)
	{
		$text = botData . '/' . $data . ".html";
		$data = file_get_contents($text);
		return $data;
	}
	
	public static function setTermsUse($data)
	{
		$text = botData . '/term-use.html';
		$data = file_put_contents($text, $data);
		return $data;
	}
	
	public static function loadInbotExample($slug)
	{
		$path = botData . 'inbot-example/' . $slug . '.html';
		return trim(file_get_contents($path));
//        return $path;
	}
	
	public static function loadInbotDocs($slug)
	{
		$path = botData . 'inbot-docs/' . $slug . '.html';
		$docs = trim(file_get_contents($path));
		if (isBeta) {
			str_replace('WinTenBot', bot_username, $docs);
		}
		return $docs;
	}
	
	/**
	 * @return string
	 */
	public static function getUrlStart(): string
	{
		$bot_username = $GLOBALS['bot_username'];
		return "https://t.me/$bot_username?start=";
	}
	
}
