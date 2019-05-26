<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 11.43
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use src\Handlers\MessageHandlers;
use src\Utils\Time;
use Longman\TelegramBot\Commands\SystemCommand;

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
		$mHandler = new MessageHandlers($message);
		$chat_id = $message->getChat()->getId();
		$leftMem = $message->getLeftChatMember();
		
		$leftMemFname = $leftMem->getFirstName();
//        $time = $message->getDate();
//	    $time = Time::jeda($time);

//        if (isset($leftMem)) {
		$mHandler->deleteMessage(); // delete event left_chat_member
		if ($message->getFrom()->getId() != $leftMem->getId()) {
			$text = "<b>Dikeluarkan : </b> {$leftMemFname} oleh " . $message->getFrom()->getFirstName();
//            $data = [
//                'chat_id' => $chat_id,
//                'text' => $text . $time,
//                'parse_mode' => 'HTML'
//            ];
		} else {
			$text = "$leftMemFname Keluar dengan sendirinya";
		}
		// $text .= "\n\nasdasd";
		$mHandler->sendText($text, '-1');
//            return Request::sendMessage($data);
//        }
	}
	
}
