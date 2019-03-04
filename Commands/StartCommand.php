<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 04/08/2018
 * Time: 23.33
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;
use src\Model\Bot;
use src\Model\Settings;
use src\Utils\Time;

class StartCommand extends SystemCommand
{
	protected $name = 'start';
	protected $description = 'Get started to Bot ';
	protected $usage = '<ping>';
	protected $version = '1.0.0';
	
	/**
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 * @throws \Exception
	 */
	public function execute()
	{
		$message = $this->getMessage();
		
		$chat_id = $message->getChat()->getId();
		$mssg_id = $message->getMessageId();
		
		$time = $message->getDate();
		$time1 = Time::jedaNew($time);
		
		$text = '🤖 <b>WinTen Bot</b> is a debugging bot, group management and other useful equipment.' .
			"\nOfficial Telegram Bot based on <b>WinTen API</b> with Anti-Spam Security!" .
			"\nFeedback and Request via @WinTenGroup or @TgBotID.\nFor help type /help. " .
			"\n\nℹ /info for more information.\nMade with ❤ by @WinTenDev";
		
		$pecah = explode('_', $message->getText(true));
		switch ($pecah[0]) {
			case 'username':
			    $file_id = isBeta ? 'CgADBQADIgADzjAhVzAzhd8G8GtBAg' : 'CgADBQADIgADzjAhV33L1C0iEGCyAg';
				return Request::sendDocument([
					'chat_id'                  => $chat_id,
					'document'                 => $file_id,
					'caption'                  => 'Buka aplikasi Telegram > Settings > Username, lalu isi Username-nya.',
					'parse_mode'               => 'HTML',
					'reply_to_message_id'      => $mssg_id,
					'disable_web_page_preview' => true,
				]);
				break;
			case 'rules':
				$settings_data = Settings::getNew(['chat_id' => $pecah[1]]);
				$text = $settings_data[0]['rules_text'] != ''
					? $settings_data[0]['rules_text']
					: 'ℹ <b>Rules</b> belum di tetapkan oleh Admin grup.';
				
				break;
			case 'eula':
				$text = Bot::getTermsUse('eula');
				
				break;
			
			case 'opensource':
				$text = bot_name . ' adalah Open Source';
				break;
			
			case '1':
				$text = 'Selamat datang di @' . bot_username;
				break;
		}
		
		if (isBeta) {
			$text = str_replace('WinTen Bot', bot_name, $text);
		}
		
		$time2 = Time::jedaNew($time);
		$time = "\n\n ⏱ " . $time1 . ' | ⏳ ' . $time2;
		
		$data = [
			'chat_id'             => $chat_id,
			'text'                => $text . $time,
			'reply_to_message_id' => $mssg_id,
			'parse_mode'          => 'HTML',
		];
		
		return Request::sendMessage($data);
	}
}
