<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 07/08/2018
 * Time: 21.30
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use src\Utils\Words;
use src\Utils\Time;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class SsCommand extends UserCommand
{
	protected $name = 'ss';
	protected $description = 'Capture web screenshot based on Thumbnail.ws API';
	protected $usage = '/ss <url>';
	protected $version = '1.0.0';
	
	/**
	 * Execute command
	 *
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function execute()
	{
		$link = 'https://winten.tk';
		$text = '';
		$message = $this->getMessage();
		$chat_id = $message->getChat()->getId();
		$mssg_id = $message->getMessageId();
		$from_id = $message->getFrom()->getId();
		$pecah = explode(' ', $message->getText());
		$repMssg = $message->getReplyToMessage();
		
		$time = $message->getDate();
		$time = Time::jeda($time);
		
		if ($repMssg != null) {
			$url = Words::extrlink($repMssg->getText(true), $pecah[1] ?? '0');
			if ($url != '') {
				$link = $url;
			} else {
				$text = "Tidak ada URL di temukan \n";
			}
		} else if ($pecah[1] != '') {
			$link = $pecah[1];
		} else {
			$text = '<b>Grab web page screenshot.</b>' .
				"\n<b>Usage : </b><code>/ss</code> (In-Reply)" .
				"\n                <code>/ss index</code> (In-Reply)" .
				"\n                <code>/ss your url here</code> (In-Message)";
			return Request::sendMessage([
				'chat_id'             => $chat_id,
				'text'                => $text . $time,
				'reply_to_message_id' => $mssg_id,
				'parse_mode'          => 'HTML'
			]);
		}
		
		$base_url = 'https://api.thumbnail.ws/api/' . thumbws_token . '/thumbnail/get?url=' . $link . '/&width=1280';
//        $img = $this->telegram->getDownloadPath() . '/pictures/' . $chat_id . $from_id . $mssg_id . 'azhekun.jpg';
//        copy($base_url, $img);
		$text .= 'Ini SS dari ' . $link;
		
		return Request::sendPhoto([
			'chat_id'             => $chat_id,
			'photo'               => $base_url,
			'caption'             => $text . $time,
			'reply_to_message_id' => $mssg_id,
			'parse_mode'          => 'HTML'
		]);
	}
}
