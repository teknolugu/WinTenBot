<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 04/08/2018
 * Time: 23.33
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Exception;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use src\Handlers\ChatHandler;
use src\Model\Bot;
use src\Model\Settings;

class StartCommand extends SystemCommand
{
	protected $name = 'start';
	protected $description = 'Get started to Bot ';
	protected $usage = '<ping>';
	protected $version = '1.0.0';
	
	/**
	 * @return ServerResponse
	 * @throws TelegramException
	 * @throws Exception
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$chatHandler = new ChatHandler($message);
		
		$chat_id = $message->getChat()->getId();
		$mssg_id = $message->getMessageId();
		
		$urlBoot = Bot::getUrlStart();
		
		
		$text = "🤖 <b>WinTen Bot</b> <code>" . versi . "</code> \nby " . federation_name . '.' .
			"\nAdalah bot debugging, manajemen grup yang di lengkapi dengan alat keamanan. " .
			'Agar fungsi saya bekerja dengan fitur penuh, jadikan saya admin dengan level standard. ' .
			
			"\n\nSaran dan fitur bisa di ajukan di @WinTenGroup atau @TgBotID.";
//			"\nMade with ❤ by " . federation_name_short;
		
		$pecah = explode('_', $message->getText(true));
		switch ($pecah[0]) {
			case 'username':
				$file_id = isBeta ? 'CgADBQADIgADzjAhVzAzhd8G8GtBAg' : 'CgADBQADIgADzjAhV33L1C0iEGCyAg';
				$caption = '<b>Ikuti video tutorial berikut.</b>' .
					"\nBuka aplikasi Telegram lalu navigasi ke Settings > Username, lalu isi Username-nya.";
				$veriv_username = [
					['text' => 'I have set Username?', 'callback_data' => 'check_username'],
				];
				return Request::sendDocument([
					'chat_id'                  => $chat_id,
					'document'                 => $file_id,
					'caption'                  => $caption,
					'parse_mode'               => 'HTML',
					'reply_to_message_id'      => $mssg_id,
					'disable_web_page_preview' => true,
					'reply_markup'             => new InlineKeyboard([
						'inline_keyboard' => array_chunk($veriv_username, 2),
					]),
				]);
				break;
			case 'rules':
				$settings_data = Settings::getNew(['chat_id' => $pecah[1]]);
				$text = $settings_data[0]['rules_text'] != ''
					? $settings_data[0]['rules_text']
					: 'ℹ <b>Rules</b> belum di tetapkan oleh Admin grup.';
				return $chatHandler->sendText($text);
				break;
			case 'eula':
				$text = Bot::getTermsUse('eula');
				return $chatHandler->sendText($text);
				break;
			
			case 'opensource':
				$text = bot_name . ' adalah Open Source';
				break;
			
			case 'help':
				$tekt = '<b>' . bot_name . '</b> <code>' . versi . '</code>' .
					"\nby " . federation_name . "\n\n" .
					Bot::loadInbotDocs('home');
				return $chatHandler->sendPrivateText($tekt, '-1', BTN_HELP_HOME);
				break;
			
			case '1':
				$text = 'Selamat datang di @' . bot_username;
				break;
		}
		
		if (isBeta) {
			$text = str_replace('WinTen Bot', bot_name, $text);
		}
		
		$btn_start = [
			['text' => 'Bantuan', 'url' => $urlBoot . 'help'],
		];
		
		return $chatHandler->sendText($text, null, $btn_start);
	}
}
