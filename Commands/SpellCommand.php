<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 04/08/2018
 * Time: 23.33
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use src\Model\Spell;

class SpellCommand extends UserCommand
{
	protected $name = 'spell';
	protected $description = 'Fix typo into corrected message';
	protected $usage = '/spell';
	protected $version = '1.0.0';
	
	/**
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 * @throws \Exception
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		
		$chat_id = $message->getChat()->getId();
		$mssg_id = $message->getMessageId();
		
		$time = $message->getDate();
		
		$repMssg = $message->getReplyToMessage();
		
		$text = "ℹ️ <i>Reply</i> pesan yang mau Spell";
		
		if ($repMssg != '') {
			$typoMssg = $repMssg->getText();
			
			$spell = Spell::fixTypo($typoMssg);
			$result = json_encode($spell);
			$text = "✅ Mungkin yang di maksud adalah:\n" . $result;
			$mssg_id = $repMssg->getMessageId();
		} else {
			$data = explode(' ', $message->getText(true));
			$datas = [
				'typo'    => $data[0],
				'fix'     => $data[1],
				'id_chat' => $message->getChat()->getId(),
				'id_user' => $message->getFrom()->getId(),
			];
			
			$result = Spell::addSpell($datas);
			$result = json_encode($result);
			
			$text = "Ya \n" . $result;
		}
		
		$data = [
			'chat_id'             => $chat_id,
			'text'                => $text,
			'reply_to_message_id' => $mssg_id,
			'parse_mode'          => 'HTML',
		];
		
		return Request::sendMessage($data);
	}
}
