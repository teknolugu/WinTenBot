<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 8/23/2018
 * Time: 6:33 PM
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use src\Utils\Time;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class LenCommand extends UserCommand
{
	protected $name = 'len';
	protected $description = 'Get length message text or caption media';
	protected $usage = '<len>';
	protected $version = '1.0.0';
	
	/**
	 * Execute command
	 *
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$chat_id = $message->getChat()->getId();
		$mssg_id = $message->getMessageId();
		$repMssg = $message->getReplyToMessage();
		
		$time = $message->getDate();
		$time1 = Time::jedaNew($time);
		
		if ($repMssg != null) {
			if ($repMssg->getCaption() != null) {
				$anu = $repMssg->getCaption();
			} else {
				$anu = $repMssg->getText();
			}
			$count = str_word_count($anu, 2);
			$arrs = array_count_values($count);
			$arrTxt = '';
			arsort($arrs);
			$i = 1;
			foreach ($arrs as $key => $data) {
				$arrTxt .= "\n" . $i . '. <code>' . $key . "</code> \t\t\t\t : " . $data . 'x';
				if ($i++ >= 10) break;
			}
			$text = "📏 <b>Panjang</b>\n-------" .
				"\n🔠 <code>" . strlen($anu) . '</code> karakter' .
				"\n🔠 <code>" . strlen(str_replace(' ', '', $anu)) . '</code> karakter (tanpa spasi)' .
				"\n🔤 <code>" . str_word_count($anu) . '</code> kata' .
				"\n\n⬆️ <b> 10 kata terbanyak</b>\n-------" . $arrTxt;
		} else {
			$text = 'Reply yang akan di gitung';
		}
		
		Request::deleteMessage([
			'chat_id'    => $chat_id,
			'message_id' => $mssg_id,
		]);
		
		$time2 = Time::jedaNew($time);
		$time = "\n\n ⏱ " . $time1 . ' | ⏳ ' . $time2;
		
		$data = [
			'chat_id'             => $chat_id,
			'text'                => $text . $time,
			'reply_to_message_id' => $repMssg->getMessageId(),
			'parse_mode'          => 'HTML',
		];
		
		return Request::sendMessage($data);
	}
}
