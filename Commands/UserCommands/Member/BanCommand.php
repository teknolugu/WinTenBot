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
use WinTenDev\Model\Translator;
use WinTenDev\Utils\Converters;

class BanCommand extends UserCommand
{
	protected $name = 'ban';
	protected $description = 'Reply to ban member';
	protected $usage = '<ban>';
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
		$message = $this->getMessage();
		$chatHandler = new ChatHandler($message);
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
			$chatHandler->sendText('Sedang mengeBan ' . $id_target);
			$kick = $chatHandler->kickMember($id_target);
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
		
		return $chatHandler->editText($text);
	}
}
