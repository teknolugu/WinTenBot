<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 03.55
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use src\Handlers\ChatHandler;
use src\Model\Fbans;
use src\Model\Group;
use src\Model\Members;
use src\Model\Settings;
use src\Utils\Buttons;
use src\Utils\Time;
use src\Utils\Words;

class NewchatmembersCommand extends SystemCommand
{
	/**
	 * Command execute method
	 *
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$chat_id = $message->getChat()->getId();
		$members = $message->getNewChatMembers();
		$chat_title = $message->getChat()->getTitle();
		$chat_username = $message->getChat()->getUsername();
//		$pinned_msg = $message->getPinnedMessage()->getMessageId();
		$chatHandler = new ChatHandler($message);
		$isKicked = false;
		$isEnableCache = true;
		$welcome_data = $isEnableCache ? Settings::readCache($chat_id) : Settings::getNew(['chat_id' => $chat_id]);
		
		// Perika apakah Aku harus keluar grup?
		if (!$message->getChat()->isPrivateChat()
			&& Group::isMustLeft($message->getChat()->getId())) {
			$chatHandler->sendText('Sepertinya saya salah alamat. Saya pamit dulu..' .
				"\nGunakan @WinTenBot", '-1');
			return Request::leaveChat(['chat_id' => $chat_id]);
		}
		
		$enable_restriction = $welcome_data[0]['enable_restriction'];
		if ($enable_restriction == '1') {
			$chatHandler->sendText('⚠ Saya benar-benar tidak untuk Grup ini!');
			return Request::leaveChat(['chat_id' => $chat_id]);
		}
		
		if ($message->botAddedInChat()) {
			$bot_name = $GLOBALS['bot_name'];
			$text = "🙋‍ Hai, perkenalkan saya <b>" . $bot_name . '</b>!' .
				"\nSaya adalah bot untuk debugging dan manajemen grup yang di lengkapi alat keamanan!" .
				"\nAgar saya bekerja dengan fitur penuh, jadikan saya admin dengan level standard. " .
				"Untuk melihat daftar perintah bisa ketikkan /help" .
				"\n\nJika kamu ingin baca dokumentasi dapat di baca di web di bawah ini";
			$btn_markup[] = ['text' => '📃 Dokumentasi', 'url' => 'https://dev.winten.tk/'];
			$send = $chatHandler->sendText($text, '-1', $btn_markup);
			if (count($message->getNewChatMembers()) == 1) return $send;
		}
		
		if ($message->getNewChatMembers()) {
			$member_names = [];
			$member_nounames = [];
			$member_bots = [];
			$member_lnames = [];
			$time_current = Time::sambuts();
			$fixed_welcome_message = '';
			$member_ids = [];
			$member_count = json_decode(Request::getChatMembersCount(['chat_id' => $chat_id]), true)['result'];
			$human_verification = $welcome_data[0]['enable_human_verification'];
			$unified_welcome = $welcome_data[0]['enable_unified_welcome'];
			$raw_welcome_message = $welcome_data[0]['splitted_welcome_message'];
			$raw_welcome_button = $welcome_data[0]['welcome_button'];
			$last_welcome_message_id = $welcome_data[0]['last_welcome_message_id'];
			
			foreach ($members as $member) {
				$full_name = trim($member->getFirstName() . ' ' . $member->getLastName());
				$nameLen = strlen(trim($full_name));
				$nameLink = "<a href='tg://user?id=" . $member->getId() . "'>" . $full_name . '</a>';
				if (Fbans::isBan($member->getId())) {
					$text = "{$member->getId()} telah terdeteksi di " . federation_name;
					$kickRes = $chatHandler->kickMember($member->getId(), true);
					if ($kickRes->isOk()) {
						$text .= " dan berhasil di tendang";
					} else {
						$text .= " dan gagal di tendang, karena <b>" . $kickRes->getDescription() . "</b>. " .
							"Pastikan saya Admin dengan level standard";
					}
					$res = $chatHandler->sendText($text, '-1');
					if (count($message->getNewChatMembers()) == 1) {
						return $res;
					}
				}
				if ($nameLen < 140) {
					if ($human_verification == '1') {
						Members::muteMember($chat_id, $member->getId(), 1);
					}
					if ($welcome_data[0]['enable_unified_welcome'] == '1') {
						$member_names[] = $nameLink;
						$new_members_count = count($member_names);
						$new_members = implode(', ', $member_names);
					} else {
//                        if ($member->getUsername() === null) {

//                        } else
						if ($member->getIsBot() === true) {
							$member_bots [] = $nameLink . ' 🤖';
							$new_bots_count = count($member_bots);
							$new_bots = implode(', ', $member_bots);
						} else {
							$member_names[] = $nameLink;
							$new_members_count = count($member_names);
							$new_members = implode(', ', $member_names);
						}
					}
					$member_nounames[] = $nameLink;
					$no_username_count = count($member_nounames);
					$no_username = implode(', ', $member_nounames);
					
					$member_ids[] = $member->getId();
					$member_id = implode(', ', $member_ids);
				} else {
					$member_lnames [] = $nameLink;
					$data = [
						'chat_id' => $chat_id,
						'user_id' => $member->getId(),
					];
					$isKicked = Request::kickChatMember($data);
					$isKicked = json_decode($isKicked, true);
					Request::unbanChatMember($data);
					
					$data = [
						'chat_id'    => $chat_id,
						'message_id' => $message->getMessageId(),
					];
					
					Request::deleteMessage($data);
				}
			}
			
			if ($unified_welcome == '1') {
				if ($raw_welcome_message != '') {
					$fixed_welcome_message = $raw_welcome_message;
				} else {
					$fixed_welcome_message = "Anggota baru : {$new_members_count}" .
						"\n👤Hai {$new_members}, selamat {$time_current}." .
						"\nSelamat datang di kontrakan {$chat_title}";
				}
			} else {
				$splitted_welcome_message = explode("\n\n", $raw_welcome_message);
				if (count($member_names) > 0) {
					if ($splitted_welcome_message[0] != '') {
						$fixed_welcome_message = $splitted_welcome_message[0];
					} else {
						$fixed_welcome_message = "Anggota baru : {$new_members_count}" .
							"\n👤Hai {$new_members}, selamat {$time_current}." .
							"\nSelamat datang di kontrakan {$chat_title}";
					}
					$fixed_welcome_message .= "\n\n";
				}
				
				if (count($member_bots) > 0) {
					if ($splitted_welcome_message[1] != '') {
						$fixed_welcome_message .= $splitted_welcome_message[1];
					} else {
						$fixed_welcome_message .= "🤖 Bot baru: {$new_bots_count}" .
							"\nHai {$new_bots}, siapa yang menambahkan kamu?.";
					}
					$fixed_welcome_message .= "\n\n";
				}
				
				if (count($member_nounames) > 0 && $unified_welcome == '0') {
					if ($splitted_welcome_message[2] != '') {
						$fixed_welcome_message .= $splitted_welcome_message[2];
					} else {
						$fixed_welcome_message .= "⚠ Tanpa username: {$no_username_count}" .
							"\nHai {$no_username}, tolong pasang username." .
							"\nJika tidak tahu caranya, klik tombol di bawah ini.";
					}
					$fixed_welcome_message .= "\n\n";
				}
			}
			if (count($member_lnames) > 0) {
				if ($isKicked['ok'] != false) {
					$text .=
						'🚷 < b>Ditendang: </b > (<code > ' . count($member_lnames) . ')</code > ' .
						"\n" . implode(', ', $member_lnames) . ', Spammer detected!';
				} else {
					$text .=
						' < b>Eksekusi : </b > Mencoba untuk menendang spammer' .
						"\n < b>Status : </b > " . $isKicked['error_code'] .
						"\n < b>Result : </b > " . $isKicked['description'];
				}
			}
			//$text .= "\n < b>Total : </b > " . $chatCount . 'Anggota';
		}
		
		$replacement = [
			'full_name'         => $full_name ?? '',
			'chat_title'        => $chat_title,
			'namelink'          => $nameLink ?? '',
			'new_members_count' => $new_members_count ?? 0,
			'new_members'       => $new_members ?? '',
			'new_bots_count'    => $new_bots_count ?? 0,
			'new_bots'          => $new_bots ?? '',
			'no_username_count' => $no_username_count ?? 0,
			'no_username'       => $no_username ?? '',
			'time_current'      => $time_current ?? '',
			'member_count'      => $member_count ?? 0,
		];
		
		$text = Words::resolveVariable(trim($fixed_welcome_message), $replacement);
		
		$btn_markup = [];
		if ($raw_welcome_button != '') {
			$btn_markup = Buttons::Generate($raw_welcome_button);
//            $btn_datas = explode(',', $raw_welcome_button);
//            foreach ($btn_datas as $key => $val) {
//                $btn_row = explode('|', $val);
//                $btn_markup[] = ['text' => $btn_row[0], 'url' => $btn_row[1]];
//            }
		}
		
		if ($no_username_count > 0) {
			$btn_markup[] = ['text' => 'Pasang username', 'url' => urlStart . 'username'];
		}
		
		if (count($member_ids) > 0 && $human_verification == '1') {
			$text .= "\n\nUntuk alasan keamanan, Silakan klik tombol <b>Verifikasi</b> di bawah ini agar tidak di Mute!";
			$btn_markup[] = ['text' => '✅ Verifikasi saya!', 'callback_data' => 'verify_' . $member_id];
		} else {
			$chatHandler->deleteMessage($last_welcome_message_id);
		}
		
		$chatHandler->deleteMessage(); // delete event new_chat_member
		
		$r = $chatHandler->sendText($text, '-1', $btn_markup);
		
		Settings::saveNew([
			'last_welcome_message_id' => $chatHandler->getSendedMessageId(),
			'chat_title'              => $chat_title,
			'chat_id'                 => $chat_id,
			'members_count'           => $member_count,
			'is_admin'                => $chatHandler->isAdmin(explode(':', bot_token)[0]),
		], [
			'chat_id' => $chat_id,
		]);
		
		if ($isEnableCache) {
			$setting_data = Settings::getNew(['chat_id' => $chat_id]);
			Settings::writeCache($chat_id, $setting_data);
		}
		
		return $r;
	}
}
