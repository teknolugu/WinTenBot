<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 12/08/2018
 * Time: 17.02
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use WinTenDev\Handlers\ChatHandler;
use WinTenDev\Utils\Console;

class DbgCommand extends UserCommand
{
	protected $name = 'dbg';
	protected $description = 'Get debug message';
	protected $usage = '<dbg>';
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
		$mHandler = new ChatHandler($message);
		$repMssg = $message->getReplyToMessage();
		$pecah = explode(' ', $message->getText(true));
		
		$mHandler->sendText('Loading JSON data..');
		if ($repMssg != null) {
			$dbg = $repMssg->getRawData();
		} else {
			$dbg = $message->getRawData();
		}
		
		if ($pecah[0] == '-r') {
			$json = json_encode($dbg, JSON_PRETTY_PRINT);
		} else {
			$json = json_encode($dbg, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
//			$json = json_encode($dbg, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		}
		
		$text = "<b>Debug messages</b>\n" . '<code>' . $json . '</code>';
		Console::println("Executed => {$message->getFullCommand()}");
		return $mHandler->editText($text);
	}
}
