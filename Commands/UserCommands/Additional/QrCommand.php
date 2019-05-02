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
use src\Utils\Time;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class QrCommand extends UserCommand
{
	protected $name = 'qt';
	protected $description = 'Generate Qr codes';
	protected $usage = '/qr';
	protected $version = '1.0.0';
	
	/**
	 * Execute command
	 *
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$chat_id = $message->getChat()->getId();
		$mssg_id = $message->getMessageId();
		$pecah = explode(' ', $message->getText());
		$repMssg = $message->getReplyToMessage();
		
		$time = $message->getDate();
		$time = Time::jeda($time);
		
		if ($repMssg != null) {
			$text = $repMssg->getText(true);
			if ($repMssg->getCaption() != '') {
				$text = $repMssg->getCaption();
			}
			$mssg_id = $repMssg->getMessageId();
		} else if ($pecah[1] != '') {
			$text = $message->getText(true);
		} else {
			$text = '<b>Generate QR from text or caption media</b>' .
				"\n<b>Usage : </b><code>/qr</code> (In-Reply)" .
				"\n                <code>/qr your text here</code> (In-Message)";
			return Request::sendMessage([
				'chat_id'             => $chat_id,
				'text'                => $text . $time,
				'reply_to_message_id' => $mssg_id,
				'parse_mode'          => 'HTML'
			]);
		}
		
		$qr = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=10&data=' . $text;
		
		return Request::sendPhoto([
			'chat_id'             => $chat_id,
			'photo'               => $qr,
			'caption'             => $time,
			'reply_to_message_id' => $mssg_id,
			'parse_mode'          => 'HTML'
		]);
	}
}
