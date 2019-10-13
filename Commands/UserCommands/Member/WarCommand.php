<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 10.43
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use WinTenDev\Handlers\ChatHandler;
use WinTenDev\Model\Group;
use WinTenDev\Model\Members;

class WarCommand extends UserCommand
{
	protected $name = 'warn';
	protected $description = 'Reply to warn member';
	protected $usage = '<warn>';
	protected $version = '1.0.0';
	
	/**
	 * Execute command
	 *
	 * @return ServerResponse|void
	 * @throws TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$chatHandler = new ChatHandler($message);
		$chat_id = $message->getChat()->getId();
		$from_id = $message->getFrom()->getId();
		$repMssg = $message->getReplyToMessage();
		$pecah = explode(' ', $message->getText(true));
		
		$isAdmin = Group::isAdmin($from_id, $chat_id);
		
		if ($repMssg != '') {
			$reason_warn = $message->getText(true) ?? '-no reason-';
			$user_id = $repMssg->getFrom()->getId();
		} else {
			return $chatHandler->sendText("Reply seseorang yang mau di war");
//			$reason_warn = $pecah[1];
//			$user_id = $pecah[0];
//			$id_target = $from_id;
		}

//		$isSudoer = Group::isSudoer($from_id);
		if ($chatHandler->isPrivateChat) {
			return $chatHandler->sendText("ℹ Warning sesorang hanya di Grub");
		}
		
		if (Group::isAdmin($user_id, $chat_id) || Group::isSudoer($user_id)) {
			return $chatHandler->sendText("ℹ Tidak dapat mewarn Admin. $user_id");
		}
		
		if ($isAdmin || $from_id == $user_id) {
			$warn_data = [
				'user_id'     => $user_id,
				'reason_warn' => $reason_warn,
				'warned_by'   => $from_id,
				'warned_from' => $chat_id,
			];
			
			$chatHandler->sendText("Sedang mewar $user_id");
			$warn = Members::addWarn($warn_data);
			
			$warns = Members::getWarn([
				'user_id' => $user_id,
			]);
			
			if ($warn->rowCount() > 0) {
				$text = "<b>$user_id Berhasil di warn</b>";
				$listWar = "";
				$no = 1;
				foreach ($warns as $key => $val) {
					$listWar .= "\n$no. " . $val['reason_warn'];
					$no++;
				}
				
				$text .= $listWar;
			} else {
				$text = "<b>$user_id gagal di warn</b>";
			}
			
			$warn_action = [
				['text' => '❤ Remove War', 'callback_data' => 'action-remove-warn_'.$user_id],
				['text' => 'diMut', 'callback_data' => 'action-mute-member_'.$user_id],
			];
			

//			$text = '<b>Status: </b> ' . Converters::intToString($kick->getOk());
//
//			if ($kick->isOk()) {
//				$text .= "\n<b>Message: </b> Kick <code>$id_target</code> berhasil..";
//			} else {
//				$text .= "\n<b>Message: </b> " . $kick->getErrorCode() . ': ' .
//					Translator::To($kick->getDescription(), 'id');
//			}
//			$text = "Not yet implemented";
			return $chatHandler->editText($text, '-1', $warn_action);
		} else {
			$text = '🚫 <i>Kamu tidak memiliki akses /kick</i>';
		}
		
		$chatHandler->editText($text);
	}
}
