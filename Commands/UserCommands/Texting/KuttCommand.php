<?php
/**
 * Created by IntelliJ IDEA.
 * User: Azhe
 * Date: 12/30/2018
 * Time: 1:52 PM
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use src\Model\KuttAPI;
use src\Utils\Time;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class KuttCommand extends UserCommand
{
	protected $name = 'kutt';
	protected $description = 'URL shortener based on Kutt API';
	protected $usage = '/kutt <url>';
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
		
		$time = $message->getDate();
		$time = Time::jeda($time);
		
		$pecah = explode(" ", $message->getText(true));
		
		$kutt = KuttAPI::submit([
			'target' => $pecah[0],
		]);

//        $kutt = json_encode($kutt, true);
		
		$kutt = $pecah[0];
		
		$data = [
			'chat_id'                  => $chat_id,
			'text'                     => $kutt . $time,
			'reply_to_message_id'      => $mssg_id,
			'parse_mode'               => 'HTML',
			'disable_web_page_preview' => true,
		];
		
		return Request::sendMessage($data);
	}
}
