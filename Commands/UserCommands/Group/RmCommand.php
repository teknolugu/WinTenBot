<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 12/08/2018
 * Time: 20.13
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use src\Handlers\ChatHandler;

class RmCommand extends UserCommand
{
	protected $name = 'purge';
	protected $description = 'Remove message only target with range';
	protected $usage = '/purge>';
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
		$chat_id = $message->getChat()->getId();
		$mssg_id = $message->getMessageId();
		$pecah = explode(' ', $message->getText(true));
		$repMssg = $message->getReplyToMessage();

//        $time = $message->getDate();
//	    $time = Time::jeda($time);
		
		$chatHandler->sendText('Initializing..');
		if ($repMssg != null) {
			$repMssgId = $repMssg->getMessageId();
			$deleted = 0;
			for ($i = $mssg_id; $i >= $repMssgId; $i--) {
				$chatHandler->editText("Deleting $i");
				$del = $chatHandler->deleteMessage($i);
				if ($del->isOk()) {
					$deleted++;
				}
			}
			$text = "\nSebanyak : " . $deleted;
		} elseif (isset($pecah[0]) && is_numeric($pecah[0])) {
			$range = $mssg_id - $pecah[0];
			$num = 0;
			for ($x = $mssg_id; $x >= $repMssg->getMessageId(); $x--) {
//                $del = Request::deleteMessage([
//                    'chat_id' => $chat_id,
//                    'message_id' => $x
//                ]);
				$del = $chatHandler->deleteMessage($x);
				if ($del->isOk()) {
					$num++;
					$chatHandler->editText("Deleting $x");
				}
			}
			$text = 'Selesai hapus ' . $num;
		} else {
			$text = 'Reply sampai mana pesan akan di del, atau jumlah ygn akan di del';
		}
		
		return $chatHandler->editText($text);
	}
}
