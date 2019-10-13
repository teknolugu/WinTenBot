<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 10.43
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Exception\TelegramException;
use WinTenDev\Handlers\ChatHandler;
use WinTenDev\Model\Group;
use WinTenDev\Model\Translator;
use WinTenDev\Utils\Converters;

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
	 * @throws TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$cHandler = new ChatHandler($message);
		$chat_id = $message->getChat()->getId();
		$from_id = $message->getFrom()->getId();
		$repMssg = $message->getReplyToMessage();
		
		$isAdmin = Group::isAdmin($from_id, $chat_id);
		
		if ($repMssg != '') {
			$id_target = $repMssg->getFrom()->getId();
		} else {
			$id_target = $from_id;
		}
//		$isSudoer = Group::isSudoer($from_id);
		
		if ($isAdmin || $from_id == $id_target) {
			$cHandler->sendText('Kicking ' . $id_target);
			$kick = $cHandler->kickMember($id_target, true);
			$text = '<b>Status: </b> ' . Converters::intToString($kick->getOk());
			
			if ($kick->isOk()) {
				$text .= "\n<b>Message: </b> Kick <code>$id_target</code> berhasil..";
			} else {
				$text .= "\n<b>Message: </b> " . $kick->getErrorCode() . ': ' .
					Translator::To($kick->getDescription(), 'id');
			}
		} else {
			$text = '🚫 <i>Kamu tidak memiliki akses /kick</i>';
		}
		
		$cHandler->editText($text);
	}
}
