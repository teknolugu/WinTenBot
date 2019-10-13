<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 11.43
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use WinTenDev\Handlers\ChatHandler;

class LeftchatmemberCommand extends SystemCommand
{
	/**
	 * @var string
	 */
	protected $name = 'leftchatmember';
	/**
	 * @var string
	 */
	protected $description = 'Left Chat Member';
	/**
	 * @var string
	 */
	protected $version = '1.0.0';
	
	/**
	 * @return ServerResponse|void
	 * @throws TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$mHandler = new ChatHandler($message);
		$from_id = $message->getFrom()->getId();
		$leftMem = $message->getLeftChatMember();
		$left_id = $leftMem->getId();
		
		$fullName = trim($message->getFrom()->getFirstName() . ' ' . $message->getFrom()->getLastName());
		$leftFullname = trim($leftMem->getFirstName() . ' ' . $leftMem->getLastName());
		
		$fullNameLink = "<a href='tg://user?id=" . $from_id . "'>" . $fullName . '</a>';
		$leftfullNameLink = "<a href='tg://user?id=" . $left_id . "'>" . $leftFullname . '</a>';
		
		$mHandler->deleteMessage(); // delete event left_chat_member
		if ($message->getFrom()->getId() != $leftMem->getId()) {
			$text = "{$fullNameLink} mengeluarkan {$leftfullNameLink}.";
		} else {
			$text = "$leftfullNameLink Keluar dari grup.";
		}
		
		$mHandler->sendText($text);
	}
	
}
