<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 04/08/2018
 * Time: 23.33
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Exception;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use src\Handlers\ChatHandler;

class GitintegrationCommand extends UserCommand
{
	protected $name = 'gitintegration';
	protected $description = 'Continuous Git event to Telegram';
	protected $usage = '<gitintegration>';
	protected $version = '1.0.0';
	
	/**
	 * @return ServerResponse
	 * @throws TelegramException
	 * @throws Exception
	 */
	public function execute()
	{
//    	return null;
		
		$message = $this->getMessage();
		$chat_id = $message->getChat()->getId();
		$chatHandler = new ChatHandler($message);
		
		$url = "https://integrate.azhe.space/$chat_id.php";
		$text = "Sambungkan bot ke repository Anda. " .
			"\nSilakan pasang url di bawah ini di pengaturan Webhook\Integrasi.\n" . $url;
		
		$text .= "\n\nPenyedia yang di dukung: GitLab, GitHub." .
			"\nUntuk konfigurasi GitHub, Content type di set ke <code>application/jeson</code>";
		
		return $chatHandler->sendText($text);
	}
}
