<?php
/**
 * Created by IntelliJ IDEA.
 * User: Azhe
 * Date: 11/23/2018
 * Time: 6:47 PM
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use src\Utils\Time;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class PinnedmessageCommand extends SystemCommand
{
	protected $name = 'pinnedmessage';
	protected $description = 'Send summary about new pinned message';
	protected $usage = 'pinnedmessage';
	protected $version = '1.0.0';
	
	/**
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$chat_id = $message->getChat()->getId();
		$chat_uname = $message->getChat()->getUsername();
		$pinnedMsg = $message->getPinnedMessage();
		
		$time = $message->getDate();
		$time1 = Time::jedaNew($time);
		
		$pinById = $message->getFrom()->getId();
		$pinByFullname =  trim($message->getFrom()->getFirstName(). ' ' . $message->getFrom()->getLastName());
		$senderId = $pinnedMsg->getFrom()->getId();
		$senderFullname =trim($pinnedMsg->getFrom()->getFirstName(). ' ' . $pinnedMsg->getFrom()->getLastName());
		$linkPin = "https://t.me/$chat_uname/".$pinnedMsg->getMessageId();
		$text = '📌 <b>Pesan disematkan baru!</b>' .
			"\n<b>Pin oleh : </b><a href='tg://user?id=$pinById'>$pinByFullname</a>".
			"\n<b>Pengirim : </b><a href='tg://user?id=$senderId'>$senderFullname</a>";
		
		// Request::deleteMessage([
		// 	'chat_id'    => $chat_id,
		// 	'message_id' => $message->getMessageId()
		// ]);
		
		$keyboard = new InlineKeyboard([
			['text' => '🔍 Baca Pesan Tersemat', 'url' => $linkPin]
		]);
		
		$time2 = Time::jedaNew($time);
		$time = "\n\n ⏱ " . $time1 . ' | ⏳ ' . $time2;
		
		$data = [
			'chat_id'      => $chat_id,
			'text'         => $text . $time,
			'parse_mode'   => 'HTML',
			'reply_markup' => $keyboard
		];
		
		return Request::sendMessage($data);
	}
}
