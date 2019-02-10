<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 04/08/2018
 * Time: 23.33
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use src\Handlers\MessageHandlers;
use src\Model\Group;
use src\Model\Spell;

class SpellCommand extends UserCommand
{
	protected $name = 'spell';
	protected $description = 'Fix typo into corrected message';
	protected $usage = '/spell';
	protected $version = '1.0.0';
	
	/**
	 * @return void
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$mHandler = new MessageHandlers($message);
		
		$repMssg = $message->getReplyToMessage();
		$data = explode(' ', $message->getText(true));
		
		if ($repMssg != '') {
			$typoMssg = $repMssg->getText();
			$mHandler->deleteMessage();
			$mHandler->sendText('Initializing..');
			$typoMssg = Spell::spellText($typoMssg);
			$mssg_id = $repMssg->getMessageId();
			$mHandler->editText("✅ Mungkin yang di maksud adalah:\n" . $typoMssg, $mssg_id);
		} elseif (count($data) == 2) {
			$isSudoer = Group::isSudoer($message->getFrom()->getId());
			if ($isSudoer) {
				$datas = [
					'typo'    => $data[0],
					'fix'     => $data[1],
					'chat_id' => $message->getChat()->getId(),
					'user_id' => $message->getFrom()->getId(),
				];
				
				$result = Spell::addSpell($datas);
				$mHandler->sendText("Ya \n" . $result);
			}
		} else {
			$mHandler->sendText('ℹ <i>Reply</i> pesan yang mau Spell');
		}
	}
}
