<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 07/09/2018
 * Time: 19.02
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use src\Model\Group;
use src\Utils\Words;
use src\Utils\Time;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;
use src\Model\Settings;

class WelcomeCommand extends UserCommand
{
	protected $name = 'welcome';
	protected $description = 'Set welcome message, buttons, others';
	protected $usage = '/welcome';
	protected $version = '1.0.0';
	
	/**
	 * Execute command
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \GuzzleHttp\Exception\GuzzleException
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
		
		$isAdmin = Group::isAdmin($from_id, $chat_id);
		$isSudoer = Group::isSudoer($from_id);
		if ($isAdmin || $isSudoer) {
			$pecah = explode(' ', $message->getText(true));
			$text = "Processing...\n";
			$data = [
				'chat_id'             => $chat_id,
				'text'                => $text . $time1,
				'reply_to_message_id' => $mssg_id,
				'parse_mode'          => 'HTML',
			];
			$mssg = Request::sendMessage($data);
			$commands = ['message', 'button'];
			if (Words::cekKata($pecah[0], $commands)) {
				$welcome_data = trim(str_replace($pecah[0], '', $message->getText(true)));
				$property = 'welcome_' . $pecah[0];
				
				$text = Settings::save([
					'chat_id'  => $chat_id,
					'property' => $property,
					'value'    => $welcome_data,
				]);
				$time2 = Time::jedaNew($time);
			} else if ($pecah[0] == '') {
				$json = json_decode(Settings::get(['chat_id' => $chat_id]), true);
				$datas = $json['result']['data'][0];
				if($datas['welcome_message'] != '') {
					$text = '<b>Welcome Message</b>' .
						"\n<code>" . $datas['welcome_message'] . '</code>';
				}else {
					$text = 'Tidak ada konfigurasi pesan welcome, silakan konfigurasi dulu';
				}
				if ($datas['welcome_button'] != '') {
					$btn_data = $datas['welcome_button'];
					$btn_datas = explode(',', $btn_data);
					foreach ($btn_datas as $key => $val) {
						$btn_row = explode('|', $val);
						$btn .= $key . ' - ' . $btn_row[0] . ' ' . $btn_row[1] . "\n";
						$btn_markup[] = ['text' => $btn_row[0], 'url' => $btn_row[1]];
					}
//					$text .= "\n\nWelcome Buton";
					$data['reply_markup'] = new InlineKeyboard([
						'inline_keyboard' => array_chunk($btn_markup, 2),
					]);
				}
				$time2 = Time::jedaNew($time);
			} else {
				$text = "Invalid parameters.\nExample /welcome message|button [data]";
				$time2 = Time::jedaNew($time);
			}
			$data['message_id'] = $mssg->result->message_id;
		} else {
			$text = 'wtf';
		}
		
		$time = "\n\n ⏱ " . $time1 . ' | ⏳ ' . $time2;
		
		$data['text'] = $text . $time;
		
		return Request::editMessageText($data);
	}
}
