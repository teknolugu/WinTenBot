<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 11/25/2018
 * Time: 6:12 AM
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use WinTenDev\Handlers\ChatHandler;
use WinTenDev\Model\Group;
use WinTenDev\Model\Members;
use WinTenDev\Model\Translator;

class DemoteCommand extends UserCommand
{
	protected $name = 'demote';
	protected $description = 'Demote chat member (bot must admin)';
	protected $usage = '/demote';
	protected $version = '1.0.0';
	
	/**
	 * Execute command
	 *
	 * @return ServerResponse
	 * @throws TelegramException
	 * @throws \ErrorException
	 */
	public function execute()
	{
		$promoteRes = null;
		$tindakan = '';
		$message = $this->getMessage();
		$mHandler = new ChatHandler($message);
		$chat_id = $message->getChat()->getId();
		$from_id = $message->getFrom()->getId();
		$repMssg = $message->getReplyToMessage();
		$pecah = explode(' ', $message->getText(true));
		
		$senderId = $message->getFrom()->getId();
		$promoteByName = trim($message->getFrom()->getFirstName() . ' ' . $message->getFrom()->getLastName());
		
		if ($repMssg != '') {
			$promotedName = trim($repMssg->getFrom()->getFirstName() . ' ' . $repMssg->getFrom()->getLastName());
			$repFrom_id = $repMssg->getFrom()->getId();
			$isAdmin = Group::isAdmin($from_id, $chat_id);
			$isSudoer = Group::isSudoer($from_id);
			if ($isAdmin || $isSudoer) {
				$mHandler->sendText('Sedang menurunkan anggota..');
				$promoteRes = Members::promote($chat_id, $repFrom_id);
			}
		} else {
			$mHandler->sendText('Sedang menurunkan anggota..');
			$promoteRes = Members::demote($chat_id, $from_id);
			
			$promotedName = $promoteByName;
		}
		
		if ($promoteRes->isOk()) {
			$text = "<a href='tg://user?id=" . $from_id . "'>$promotedName</a> tidak menjadi Admin " .
				"\nDiturunkan oleh <a href='tg://user?id=$senderId'>" . $promoteByName . '</a>';
		} else {
			$text = '<b>🚫 Status : </b><code>' .
				Translator::To($promoteRes->getDescription(), 'id') . '.</code>';
		}
		
		return $mHandler->editText($text);
	}
}
