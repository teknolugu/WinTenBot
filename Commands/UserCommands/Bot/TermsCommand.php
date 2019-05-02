<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 04/08/2018
 * Time: 23.33
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Exception;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use src\Model\Bot;
use src\Utils\Time;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class TermsCommand extends UserCommand
{
	protected $name = 'terms';
	protected $description = 'Read EULA';
	protected $usage = '/terms';
	protected $version = '1.0.0';
	
	/**
	 * @return ServerResponse
	 * @throws TelegramException
	 * @throws Exception
	 */
	public function execute()
	{
		$message = $this->getMessage();
		
		$chat_id = $message->getChat()->getId();
		$mssg_id = $message->getMessageId();
		
		$time = $message->getDate();
		$time1 = Time::jedaNew($time);

//        $urlStart = 'https://t.me/' . bot_username . '?start=';
//        $btn_data = [
//            ['text' => 'End User License', 'url' => $urlStart . 'eula'],
//            ['text' => 'Privacy Policy', 'url' => $urlStart],
//            ['text' => 'Terms Of Use', 'url' => $urlStart],
//            ['text' => 'Open Source', 'url' => $urlStart . 'opensource'],
//            ['text' => 'Documentation', 'url' => $urlStart],
//            ['text' => 'How to set Username', 'url' => $urlStart . 'username']
//        ];
		
		if ($message->getChat()->isPrivateChat()) {
			$btn_data = array_chunk(BTN_TERMS_WITH_CALLBACK, 2);
		} else {
			$btn_data = array_chunk(BTN_TERMS_WITHOUT_CALLBACK, 2);
		}
		
		$text = "Klik salah satw untuk baca salah satu";
		
		$time2 = Time::jedaNew($time);
		$time = "\n\n ⏱ " . $time1 . " | ⏳ " . $time2;
		
		$data = [
			'chat_id'             => $chat_id,
			'text'                => $text . $time,
			'reply_to_message_id' => $mssg_id,
			'parse_mode'          => 'HTML',
			'reply_markup'        => new InlineKeyboard([
				'inline_keyboard' => $btn_data,
			]),
		];
		
		return Request::sendMessage($data);
	}
}
