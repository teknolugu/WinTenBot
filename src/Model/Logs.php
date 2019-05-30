<?php

namespace src\Model;

use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class Logs
{
	/**
	 * @param $data
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public static function toSudoer($data)
	{
		$sudoers = sudoer;
		foreach ($sudoers as $sudo) {
			self::toChannel(['text' => $sudo]);
			return Request::sendMessage([
				'text'       => $data['text'],
				'chat_id'    => $sudo,
				'parse_mode' => 'HTML',
			]);
		}
	}
	
	/**
	 * @param      $text
	 * @param null $keyboard
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public static function toChannel($text, $keyboard = null)
	{
		$chat_id = '-1001353021674';
		$data = [
			'text'       => $text,
			'chat_id'    => $chat_id,
			'parse_mode' => 'HTML',
		];
		if ($keyboard != null) {
			$data['reply_markup'] = new InlineKeyboard([
				'inline_keyboard' => array_chunk($keyboard, 2),
			]);
		}
		return Request::sendMessage($data);
	}
}
