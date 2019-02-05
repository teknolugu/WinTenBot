<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 10/09/2018
 * Time: 07.55
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use src\Model\Group;
use src\Utils\Time;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;
use src\Model\Settings;

class SettingCommand extends UserCommand
{
	protected $name = 'set';
	protected $description = 'Seting up group';
	protected $usage = '/set <param> <value>';
	protected $version = '1.0.0';
	
	/**
	 * Execute command
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$chat_id = $message->getChat()->getId();
		$mssg_id = $message->getMessageId();
		$from_id = $message->getFrom()->getId();
		$time = $message->getDate();
		$time1 = Time::jedaNew($time);
		$pecah = explode(' ', $message->getText(true));
		
		$isAdmin = Group::isAdmin($from_id, $chat_id);
		$isSudoer = Group::isSudoer($from_id);
		if ($isAdmin || $isSudoer) {
//            $data = [
//                'chat_id' => $chat_id,
//                'parse_mode' => 'HTML'
//            ];
//
			if ($pecah[0] != '') {
//                $hasil = Grup::simpanSet([
//                    'id_grup' => $chat_id,
//                    'properti' => $pecah[1],
//                    'data' => $pecah[2]
//                ]);
//                $hasil = json_decode($hasil, true);
//                $text = 'Set' . "\n " . $pecah[1] . ' ' . $pecah[2] . ' ' . $hasil['status'];
//                $text = json_encode($hasil);
				
				Settings::save($chat_id, 'test', 'wik');
				$text = 'wik';
			} else {
				$switch_element = mt_rand(0, 9) < 5 ? 'true' : 'false';
				$inline_keyboard = new InlineKeyboard([
					['text' => 'inline', 'switch_inline_query' => $switch_element],
					['text' => 'inline current chat', 'switch_inline_query_current_chat' => $switch_element],
				], [
					['text' => 'Welcome', 'callback_data' => 'identifier'],
					['text' => 'Username', 'url' => 'https://github.com/php-telegram-bot/core'],
				], [
					['text' => 'callback', 'callback_data' => 'identifier'],
					['text' => 'open url', 'url' => 'https://github.com/php-telegram-bot/core'],
				]);
				$data['chat_id'] = $from_id;
				$data['reply_markup'] = $inline_keyboard;
				$text = "Lorem ipsum dolor";
			}
		}
		
		$time2 = Time::jedaNew($time);
		$time = "\n\n ⏱ " . $time1 . " | ⏳ " . $time2;
		
		Request::deleteMessage([
			'chat_id'    => $chat_id,
			'message_id' => $mssg_id,
		]);
		
		$data['text'] = $text . $time;
		
		return Request::sendMessage($data);
	}
}
