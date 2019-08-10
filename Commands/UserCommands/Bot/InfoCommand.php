<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 8/24/2018
 * Time: 4:46 PM
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use src\Handlers\ChatHandler;
use src\Model\Bot;

class InfoCommand extends UserCommand
{
	protected $name = 'info';
	protected $description = 'Get information about Me';
	protected $usage = '<info>';
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
		$chatHandler = new ChatHandler($message);
		
		$text = '🤖 <b>WinTen Beta Bot</b> <code>' . versi . "</code>\n" . descBot;
		$bot_name = Bot::getBotName();
		
		if (isBeta) {
			$text .= descBeta;
		}
		
		$text .= "\n\nFor more fast and keep fast Bot and still continue improvement and reability, please <b>Donate</b> below for buy VPS and give me coffe.";
		
		$text = str_replace("WinTen Beta Bot", $bot_name, $text);
		
		$keyboard = [
			['text' => '👥 WinTen Group', 'url' => 'https://t.me/WinTenGroup'],
			['text' => '❤ by WinTenDev', 'url' => 'https://t.me/WinTenDev'],
			['text' => '👥 Redmi 5A (Riva) ID', 'url' => 'https://t.me/Redmi5AID'],
			['text' => '👥 Telegram Bot API', 'url' => 'https://t.me/TgBotID'],
			['text' => '💽 Source code', 'url' => 'https://github.com/WinTenDev/WinTenBot'],
			['text' => '🏗 Akmal Projext', 'url' => 'https://t.me/AkmalProjext'],
			['text' => '💰 Donate', 'url' => 'http://paypal.me/Azhe403'],
		];
		
		return $chatHandler->sendText($text, '-1', $keyboard);
	}
}
