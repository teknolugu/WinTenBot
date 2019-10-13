<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 09.46
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use WinTenDev\Utils\Time;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

class PrivatenotifCommand extends SystemCommand
{
	protected $version = '1.0.0';
	
	/**
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		
		$chat_id = $message->getChat()->getId();
		$chatp_id = $message->getReplyToMessage();
		
		$time = $message->getDate();
		$time = Time::jeda($time);
		
		$data = [
			'chat_id'    => $chatp_id,
			'text'       => '<b>Pong..!!</b>' . $time,
			'parse_mode' => 'HTML'
		];
		
		return Request::sendMessage($data);
	}
}
