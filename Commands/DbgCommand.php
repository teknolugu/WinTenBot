<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 12/08/2018
 * Time: 17.02
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use src\Handlers\MessageHandlers;

class DbgCommand extends UserCommand
{
	protected $name = 'dbg';
	protected $description = 'Get debug message';
	protected $usage = '<dbg>';
	protected $version = '1.0.0';
	
	/**
	 * Execute command
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$mHandler = new MessageHandlers($message);
		$repMssg = $message->getReplyToMessage();
		
		$mHandler->sendText('Loading JSON data..');
		if ($repMssg != null) {
			$dbg = $repMssg->getRawData();
		} else {
			$dbg = $message->getRawData();
		}
		
		$text = "<b>Debug messages</b>\n" .
			'<code>' .
			json_encode($dbg, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) .
			'</code>';
		
		return $mHandler->editText($text);
	}
}
