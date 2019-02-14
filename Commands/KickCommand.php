<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 10.43
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use src\Handlers\ChatHandler;
use src\Handlers\MessageHandlers;
use src\Model\Group;

class KickCommand extends UserCommand
{
	protected $name = 'kick';
	protected $description = 'Reply to kick member';
	protected $usage = '<kick>';
	protected $version = '1.0.0';
	
	/**
	 * Execute command
	 *
	 * @return void
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$cHandler = new ChatHandler($message);
		$mHandler = new MessageHandlers($message);
		$chat_id = $message->getChat()->getId();
		$from_id = $message->getFrom()->getId();
		$repMssg = $message->getReplyToMessage();
		
		$isAdmin = Group::isAdmin($from_id, $chat_id);
		$isSudoer = Group::isSudoer($from_id);
		if ($isAdmin || $isSudoer) {
			$id_target = $repMssg->getFrom()->getId();
			$mHandler->sendText('Kicking ' . $id_target);
			$kick = $cHandler->kickMember($id_target, true);
			$text = '<b>Success: </b> ' . $kick->isOk();
			
			if ($kick->isOk()) {
				$text .= "\n<b>Message: </b> Kick berhasil..";
			} else {
				$text .= "\n<b>Code: </b>" . $kick->getErrorCode() .
					"\n<b>Desc: </b> " . $kick->getDescription();
			}
		} else {
			$text = '🚫 <i>Kamu tidak memiliki akses /kick</i>';
		}
		
		$mHandler->editText($text);
	}
}
