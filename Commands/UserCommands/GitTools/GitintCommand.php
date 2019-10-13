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
use WinTenDev\Handlers\ChatHandler;
use WinTenDev\Model\Bot;

class GitintCommand extends UserCommand
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
		
		$bot_username = Bot::getBotUsername();
		
		
		$url = "https://integrate.winten.space/$chat_id.php";
		$text = "🔌 Sambungkan bot ke repository Anda. " .
			"\nSilakan pasang url di bawah ini di pengaturan Webhook\Integrasi.\n" . $url;
		
		$text .= "\n\nPenyedia yang di dukung: GitLab, GitHub." .
			"\nUntuk konfigurasi GitHub, <b>Content type</b> di set ke <code>application/jeson</code>";
		
		if($bot_username!="WinTenDevBot"){
			$text.= "\n\nKami menganjurkan menggunakan @WinTenDevBot untuk bot manajemen grup yang di lengkapi tools untuk Pengembang yang lebih canggih.";
		}
		
		return $chatHandler->sendText($text);
	}
}
