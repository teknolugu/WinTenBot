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
use WinTenDev\Handlers\ChatHandler;
use WinTenDev\Model\Bot;
use WinTenDev\Model\Fbans;
use WinTenDev\Model\Group;
use WinTenDev\Model\MalFiles;
use WinTenDev\Model\Members;
use WinTenDev\Model\Settings;
use WinTenDev\Model\UrlLists;
use WinTenDev\Model\Wordlists;
use WinTenDev\Utils\Converters;
use WinTenDev\Utils\Folder;
use WinTenDev\Utils\Inputs;

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
		$bot_name = Bot::getBotName();
		
		$isBeta = Inputs::globals('is_beta');
		
		// SWITCT LEVEL 1
		switch ($bacot[0]) {
			// 1. level 1
			case 'start': // Start
				
				// SWITH LEVEL 2
				$this->callbackStart($bacot);
				break; // End Start
			
			// 2. LEVEL 1
			case 'general':
				switch ($bacot[1]){
					case 'close-all':
						if($bacot[2] == "admin"){
							if($chatHandler->isAdmin($callback_from_id)){
//								$text = "All completed";
								$chatHandler->deleteMessage($callback_query->getMessage()->getMessageId());
							}else{
								$text = "🚫 401: Unauthorized access." .
									"\nKamu bukan admin di Grup ini.";
							}
						}
						$chatHandler->answerCallbackQuery($text);
						break;
						
					case'anu':
						$chatHandler->editText($bacot[1], '-1', BTN_OK_NO_CANCEL);
					break;
				}
				
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
				
				break;
			
			// 3. Case HELP CALLBACK LEVEL 1
			case 'help':
				$splitHelp = explode('/', $bacot[1]);
				$text = '<b>' . $bot_name . '</b> <code>' . versi . '</code>' .
					"\nby <b>WinTenDev ES2 (Elastic Security System)</b>\n\n";
				$text .= Bot::loadInbotDocs($bacot[1]);
				
				//SWITCH LEVEL 2
				switch ($splitHelp[0]) {
					case 'home':
						$btn_markup = BTN_HELP_HOME;
						break;
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
					case 'welcome':
						$btn_markup = BTN_HELP_WELCOME;
						break;
//					default:
//						$btn_markup = BTN_HELP_HOME;
//						break;
				}
				
				if(isset($splitHelp[1])) {
					switch ($splitHelp[1]) {
						case 'core':
							$btn_markup = BTN_HELP_CORE;
							break;
						case 'info':
							$btn_markup = BTN_HELP_INFO;
							break;
						case 'welcome':
							$btn_markup = BTN_HELP_WELCOME;
							break;
					}
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
				if ($isAdmin || $chatHandler->isPrivateChat) {
					Settings::toggleSetting([
						'chat_id' => $callback_chat_id,
						'toggle'  => 'enable' . ltrim($callback_data, $bacot[0]),
					]);
					
					$text = "✅ Saved " . ucfirst(ltrim($callback_data, 'setting_'));
					$edit = '⚙ Group settings for <b>' . $callback_chat_title . '</b>' . "\n\n" . $text;
					
					$btns = Settings::getForTombol($callback_chat_id, $chatHandler->isPrivateChat);
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
						if ($chatHandler->isAdmin($callback_from_id)) {
//							$chatHandler->deleteMessage($bacot[2]);
							Request::deleteMessage([
								'chat_id'  => $bacot[3],
								'message_id' => $bacot[2]
							]);
						} else {
							$text = "🚫 401: Unauthorized access." .
								"\nKamu bukan admin di Grup ini.";
						}
						
						break;
					
					case 'kick-member':
						if ($chatHandler->isAdmin($callback_from_id)) {
//							$text = "Banned : {$bacot[2]}";
							$chatHandler->kickMember([
								'chat_id' => $bacot[3],
								'user_id' => $bacot[2],
							], true);
						} else {
							$text = "🚫 401: Unauthorized access." .
								"\nKamu bukan admin di Grup ini.";
						}
						
						break;
					
					case 'ban-member':
						if($chatHandler->isAdmin($callback_from_id)){
							$chatHandler->kickMember([
								'chat_id' => $bacot[3],
								'user_id' => $bacot[2],
							]);
						}else{
							$text = "🚫 401: Unauthorized access." .
								"\nKamu bukan admin di Grup ini.";
						}
						
						break;
					case 'unmute-member':
						if($chatHandler->isAdmin($callback_from_id)){
							$r = $chatHandler->unrestrictMember($bacot[2]);
							$chatHandler->editMessageCallback("Anggota berhasil di unmute");
							return $chatHandler->deleteMessage($chatHandler->callBackMessageId, 3);
						}else{
							$text = "🚫 401: Unauthorized access." .
								"\nKamu bukan admin di Grup ini.";
						}
						break;
					
					case 'instant-fban':
						if ($chatHandler->isAdmin($callback_from_id)) {
							$fbans_data = [
								'user_id'     => $bacot[2],
								'reason_ban'  => 'instant-fban by' . $callback_from_id,
								'banned_by'   => $chatHandler->from_id,
								'banned_from' => $message->getChat()->getId(),
							];
							
							$chatHandler->kickMember($bacot[2], true);
							
							$fban = Fbans::saveFBans($fbans_data);
							Request::forwardMessage([
								'chat_id'      => log_channel,
								'from_chat_id' => $chatHandler->chatId,
								'message_id'   => $bacot[3],
							]);
							
							$text = "✅ Global Banned succesfully.";
							$writeFban = Fbans::writeCacheFbans();
						} else {
							$text = "🚫 401: Unauthorized access." .
								"\nKamu bukan admin di Grup ini.";
						}
						
						break;
				}
				
				$aksi = str_replace('-', ' ', ucwords($bacot[1]));
				
				$chatHandler->answerCallbackQuery($text);
				
				if ($r->isOk()) {
					$text = "Aksi $aksi berhasil.";
					$reportAction = "\nAksi <b>$aksi</b> di lakukan oleh $callback_from_id";
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
			
			case 'filtering-message':
				if ($chatHandler->isSudoer($callback_from_id)) {
					$text = federation_name_short . " Filtering Message\n";
					switch ($bacot[1]) {
						case'file':
							$lists = MalFiles::getAll();
							$list = '';
							ksort($lists);
							$countList = count($lists);
							if ($countList > 0) {
								foreach ($lists as $lis) {
									$list .= '<code>' . $lis['file_id'] . "</code>\n";
								}
							} else {
								$list = 'No <b>Files</b> blocked globally';
							}
							
							$text .= "📜 <b>Url-Lists</b>: <code>$countList</code>\n" .
								"===============================\n" .
								trim($list);
							break;
						
						case 'kata':
							$wordlists = Wordlists::getAll();
							$list = '';
							ksort($wordlists);
							$countWordlist = count($wordlists);
							foreach ($wordlists as $word) {
								$list .= $word['word'] . ' -> ' . $word['class'] . "\n";
							}
							$text .= "📜 <b>Wordlist</b>: <code>$countWordlist</code>\n" .
								"===============================\n" .
								trim($list);
							break;
						
						case 'url':
							$lists = UrlLists::getAll();
							$list = '';
							ksort($lists);
							$countList = count($lists);
							if ($countList > 0) {
								foreach ($lists as $lis) {
									$list .= $lis['url'] . ' -> ' . $lis['class'] . "\n";
								}
							} else {
								$list = 'No <b>Url</b> blocked globally';
							}
							$text = "📜 <b>Url-Lists</b>: <code>$countList</code>\n" .
								"===============================\n" .
								trim($list);
							break;
						
						case 'fedban':
							$lists = '';
							$fbans = Fbans::getAdminFbansAll();
							$countAdmin = count($fbans);
							if ($countAdmin > 0) {
								foreach ($fbans as $fban) {
									$lists .= Converters::intToEmoji(!$fban['is_banned']) . ' ' .
										$fban['user_id'] . ' from ' .
										$fban['promoted_from'] . "\n";
								}
							} else {
								$lists = 'No Admin FBans';
							}
							
							$text = '<b>Admin Fbans Lists</b>: ' . $countAdmin .
								"\n-------------------------------------------------\n" . trim($lists);
							break;
					}
					
					$chatHandler->editMessageCallback($text . $bacot[1],
						null,
						BUTTON_FILTERING_MESSAGE);
				} else {
					$chatHandler->answerCallbackQuery('wik wik');
				}
				
				break;
			
			case 'cache':
				$canReset = false;
				if ($chatHandler->isAdmin($callback_from_id)
					|| $chatHandler->isSudoer($callback_from_id)
					|| $chatHandler->isPrivateChat) {
					$canReset = true;
				}
				
				if ($canReset) {
					$base_dir = botData . 'cache-json/' . $chatHandler->getChatId();
					switch ($bacot[1]) {
						case'tags':
							$dir = $base_dir . '/tags.json';
							break;
						case 'setting':
							$dir = $base_dir . '/setting.json';
							break;
						default:
							$dir = $base_dir;
							break;
					}
					
					if (is_dir($dir)) {
						$delete = Folder::deleteDir($dir);
					} else {
						$delete = Folder::deleteFile($dir);
					}
					
					if ($delete) {
						$text = "Cache {$bacot[1]} berhasil di hapus.";
					} else {
						$text = "Cache {$bacot[1]} sudah di hapus.";
					}
				} else {
					$text = "Kamu bukan Admin di Grup ini.";
				}
				$res = $chatHandler->answerCallbackQuery($text);
				break;
		}
		
		$isBeta = Inputs::globals('is_beta');
		if ($isBeta) {
			$text = $callback_data;
		} else {
			$text = "🚫 Sesuatu telah terjadi.";
		}
		$res = $chatHandler->answerCallbackQuery($text);

//		$data = [
//			'callback_query_id' => $callback_query_id,
//			'text'              => $text,
//			'show_alert'        => true,
//			'cache_time'        => 5,
//		];
//		return Request::answerCallbackQuery($data);
		return $res;
	}
	
	/**
	 * @param $bacot
	 * @return ServerResponse
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
