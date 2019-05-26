<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use src\Handlers\ChatHandler;
use src\Model\Bot;
use src\Model\Group;
use src\Model\Members;
use src\Model\Settings;
use src\Utils\Converters;

/**
 * Callback query command
 *
 * This command handles all callback queries sent via inline keyboard buttons.
 *
 * @see InlineCommand.php
 */
class CallbackqueryCommand extends SystemCommand
{
	/**
	 * @var string
	 */
	protected $name = 'callbackquery';
	
	/**
	 * @var string
	 */
	protected $description = 'Reply to callback query';
	
	/**
	 * @var string
	 */
	protected $version = '1.1.1';
	
	/**
	 * Command execute method
	 *
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function execute()
	{
		$callback_query = $this->getCallbackQuery();
		$callback_query_id = $callback_query->getId();
		$callback_data = $callback_query->getData();
		$callback_id = $callback_query->getMessage()->getMessageId();
		$callback_chat_id = $callback_query->getMessage()->getChat()->getId();
		$callback_from_id = $callback_query->getFrom()->getId();
		$callback_from_username = $callback_query->getFrom()->getUsername();
		$callback_mssg_text = $callback_query->getMessage()->getText();
		$callback_chat_title = $callback_query->getMessage()->getChat()->getTitle();
		
		$message = $callback_query->getMessage();
		$chatHandler = new ChatHandler($message);
		$chatHandler->callBackQueryId = $callback_query->getId();
		$chatHandler->callBackMessageId = $callback_query->getMessage()->getMessageId();
		
		$bacot = explode('_', $callback_data);
		
		// SWITCT LEVEL 1
		switch ($bacot[0]) {
			// 1. level 1
			case 'start': // Start
				
				// SWITH LEVEL 2
				$this->callbackStart($bacot);
				break; // End Start
			
			// 2. LEVEL 1
			case 'general':
//	        	$chatHandler->editText('wik',null,BTN_OK_NO_CANCEL);
//				Request::editMessageText([
//					'chat_id'      => $callback_query->getMessage()->getChat()->getId(),
//					'message_id'   => $callback_id,
//					'parse_mode'   => 'HTML',
//					'reply_markup' => new InlineKeyboard([
//						'inline_keyboard' => array_chunk(BTN_OK_NO_CANCEL, 3),
//					]),
//					'text'         => $bacot[1],
//				]);
				$chatHandler->editText($bacot[1], '-1', BTN_OK_NO_CANCEL);
				break;
			
			// 3. Case HELP CALLBACK LEVEL 1
			case 'help':
				$splitHelp = explode('/', $bacot[1]);
				$text = '<b>' . bot_name . '</b> <code>' . versi . '</code>' .
					"\nby <b>WinTenDev ES2 (Elastic Security System)</b>\n\n";
				$text .= Bot::loadInbotDocs($bacot[1]);
				
				//SWITCH LEVEL 2
				switch ($splitHelp[0]) {
					case 'core':
						$btn_markup = BTN_HELP_CORE;
						break;
					case 'info':
						$btn_markup = BTN_HELP_INFO;
						break;
					case 'group':
						$btn_markup = BTN_HELP_GROUP;
						break;
					case 'member':
						$btn_markup = BTN_HELP_MEMBER;
						break;
					case 'tag':
						$btn_markup = BTN_HELP_TAGGING;
						break;
					case 'additional':
						$btn_markup = BTN_HELP_ADDITIONAL;
						break;
					case 'fedban':
						$btn_markup = BTN_HELP_FEDBAN;
						break;
					case 'texting':
						$btn_markup = BTN_HELP_TEXTING;
						break;
					default:
						$btn_markup = BTN_HELP_HOME;
						break;
				}
				
				switch ($splitHelp[1]) {
					case 'core':
						$btn_markup = BTN_HELP_CORE;
						break;
					case 'info':
						$btn_markup = BTN_HELP_INFO;
						break;
				}
				
				return $chatHandler->editMessageCallback($text, '-1', $btn_markup);
				break;
			
			case 'verify':
				$need_verif = ltrim($callback_data, 'verify_');
				$id_lists = explode(' ', $need_verif);
				if (in_array($callback_from_id, $id_lists)) {
					foreach ($id_lists as $id) {
						if ($id == $callback_from_id) {
							Members::muteMember($callback_chat_id, $id, -1);
							$text = 'Terima kasih sudah memverifikasi';
						}
					}
				} else {
					$text = 'Kamu telah memverifikasi';
				}
				return $chatHandler->answerCallbackQuery($text);
				break;
			
			case 'setting':
				$isAdmin = Group::isAdmin($callback_from_id, $callback_chat_id);
				if ($isAdmin) {
					Settings::toggleSetting([
						'chat_id' => $callback_chat_id,
						'toggle'  => 'enable' . ltrim($callback_data, $bacot[0]),
					]);
					
					$text = "✅ Saved " . ucfirst(ltrim($callback_data, 'setting_'));
					$edit = '⚙ Group settings for <b>' . $callback_chat_title . '</b>' . "\n\n" . $text;
					
					$btns = Settings::getForTombol(['chat_id' => $callback_chat_id]);
					$btn_markup = [];
					$btns = array_map(null, ...$btns);
					foreach ($btns as $key => $val) {
						$p = explode('_', $key);
						$cek = Converters::intToEmoji($val);
						$callback = ltrim($key, $p[0]);
						$btn_text = str_replace('_', ' ', ltrim($callback, '_'));
						$btn_markup[] = [
							'text'          => $cek . ' ' . ucfirst($btn_text),
							'callback_data' => 'setting' . $callback,
						];
					}
					
					Request::editMessageText([
						'chat_id'      => $callback_chat_id,
						'message_id'   => $callback_id,
						'parse_mode'   => 'HTML',
						'reply_markup' => new InlineKeyboard([
							'inline_keyboard' => array_chunk($btn_markup, 2),
						]),
						'text'         => $edit,
					]);
				} else {
					$text = '401: Unauthorized.';
				}
				
				break;
			
			case 'check':
				if ($callback_from_username != '') {
					if ($callback_from_id == $bacot[1]) {
						$r = $chatHandler->restrictMember($bacot[1]);
						if ($r->isOk()) {
							$text = 'Terima kasih sudah mengatur Username.' .
								"\nUsername kamu adalah: $callback_from_username";
							$chatHandler->answerCallbackQuery($text);
							return $chatHandler->deleteMessage($chatHandler->getSendedMessageId(), 3);
						}
					} else {
						$text = 'Tombol ini bukan untukmu bep';
					}
				} else {
					$text = 'Kamu belum menetapkan Username. Silakan ikuti tutorial video tersebut';
				}
				return $chatHandler->answerCallbackQuery($text);
				break;
			
			case 'inbot-example':
				$isAdmin = Group::isAdmin($callback_from_id, $callback_chat_id);
				if ($isAdmin) {
					$edit = Bot::loadInbotExample($bacot[1]);
					$btn_markup = [
						['text' => 'Contoh Message', 'callback_data' => 'inbot-example_welcome-message-example'],
						['text' => 'Contoh Button', 'callback_data' => 'inbot-example_welcome-button-example'],
					];
					return Request::editMessageText([
						'chat_id'                  => $callback_chat_id,
						'message_id'               => $callback_id,
						'parse_mode'               => 'HTML',
						'reply_markup'             => new InlineKeyboard([
							'inline_keyboard' => array_chunk($btn_markup, 2),
						]),
						'text'                     => $edit,
						'disable_web_page_preview' => true,
					]);
				}
//                else{
//                    $text = "You isn't Admin in this Group";
//                }
				break;
			
			// Level 1
			case 'action':
				
				// SWITCH LEVEL 2
				switch ($bacot[1]) {
					case 'delete-message':
						$r = Request::deleteMessage([
							'chat_id'    => $bacot[3],
							'message_id' => $bacot[2],
						]);
//						$chatHandler->deleteMessage($bacot[2]);
						break;
					
					case 'kick-member':
						$r = Request::kickChatMember([
							'chat_id' => $bacot[3],
							'user_id' => $bacot[2],
						]);
						$r = Request::unbanChatMember([
							'chat_id' => $bacot[3],
							'user_id' => $bacot[2],
						]);
						break;
					
					case 'ban-member':
						$r = Request::kickChatMember([
							'chat_id' => $bacot[3],
							'user_id' => $bacot[2],
						]);
						break;
				}
				
				$aksi = str_replace('-', ' ', ucwords($bacot[1]));
				if ($r->isOk()) {
					$text = "Aksi $aksi berhasil.";
					$reportAction = 'Wih siapa yg anuin?' .
						"\nAksi <b>$aksi</b> di lakukan oleh $callback_from_id";
					Request::sendMessage([
						'chat_id'    => $bacot[3],
						'parse_mode' => 'HTML',
						'text'       => $reportAction,
					]);
				} else {
					$text = "Aksi $aksi gagal." .
						"\n" . $r->getDescription();
				}
				
				return $chatHandler->editText($text);
				
				break;
		}

//		$data = [
//			'callback_query_id' => $callback_query_id,
//			'text'              => $text,
//			'show_alert'        => true,
//			'cache_time'        => 5,
//		];
//		return Request::answerCallbackQuery($data);
	}
	
	/**
	 * @param $bacot
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	private function callbackStart($bacot)
	{
		$message = $this->getCallbackQuery()->getMessage();
		$handler = new ChatHandler($message);
		if ($bacot[1] == 'terms') {
			$btn_data = array_chunk(BTN_TERMS_WITH_CALLBACK, 2);
			
			// SWITCH LEVEL 3
			switch ($bacot[2]) {
				case 'eula':
					$text = Bot::getTermsUse('eula');
					If (isBeta) {
						$text = str_replace('WinTen Bot', bot_name, $text);
					}
					break;
				case 'opensource':
					$text = bot_name . ' adalah Open Source';
					break;
			}
			
			$r = $handler->editText($text, $message->getMessageId(), $btn_data);
//				Request::editMessageText([
//					'chat_id'      => $callback_query->getMessage()->getChat()->getId(),
//					'message_id'   => $callback_id,
//					'parse_mode'   => 'HTML',
//					'reply_markup' => new InlineKeyboard([
//						'inline_keyboard' => $btn_data,
//					]),
//					'text'         => $text,
//				]);
		}
		return $r;
	}
	
	private function callbackCheck($bacot)
	{
	}
}
