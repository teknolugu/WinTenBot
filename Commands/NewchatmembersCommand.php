<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 03.55
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use App\Grup;
use App\Kata;
use App\Waktu;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;
use src\Model\Settings;

class NewchatmembersCommand extends SystemCommand
{
	/**
	 * Command execute method
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	public function execute()
	{
		$text = '';
		$message = $this->getMessage();
		$chat_id = $message->getChat()->getId();
		$members = $message->getNewChatMembers();
		$chat_title = $message->getChat()->getTitle();
		$chat_uname = $message->getChat()->getUsername();
//		$pinned_msg = $message->getPinnedMessage()->getMessageId();
		$isKicked = false;
		
		$time = $message->getDate();
		$time1 = Waktu::jedaNew($time);
		
		// Perika apakah Aku harus keluar grup?
		if (isRestricted
			&& !$message->getChat()->isPrivateChat()
			&& Grup::isMustLeft($message->getChat()->getId())) {
			$text = 'Sepertinya saya salah alamat. Saya pamit dulu..' . "\nGunakan @WinTenBot";
			Request::sendMessage([
				'chat_id'    => $chat_id,
				'text'       => $text,
				'parse_mode' => 'HTML',
			]);
			return Request::leaveChat(['chat_id' => $chat_id]);
		}
		
		if ($message->botAddedInChat() || $message->getNewChatMembers()) {
			$member_names = [];
			$member_nounames = [];
			$member_bots = [];
			$member_lnames = [];
			$time_current = Waktu::sambuts();
			$new_welcome_message = '';
			
			$data = [
				'chat_id'    => $chat_id,
				'message_id' => $message->getMessageId(),
			];
			
			Request::deleteMessage($data);
			
			foreach ($members as $member) {
				$full_name = trim($member->getFirstName() . ' ' . $member->getLastName());
				$nameLen = strlen($full_name);
				$nameLink = "<a href='tg://user?id=" . $member->getId() . "'>" . $full_name . '</a>';
				if ($nameLen < 140) {
					if ($member->getUsername() === null) {
						$member_nounames[] = $nameLink;
						$no_username_count = count($member_nounames);
						$no_username = implode(', ', $member_nounames);
					} else if ($member->getIsBot() === true) {
						$member_bots [] = $nameLink . ' 🤖';
						$new_bots_count = count($member_bots);
						$new_bots = implode(', ', $member_bots);
					} else {
						$member_names[] = $nameLink;
						$new_members_count = count($member_names);
						$new_members = implode(', ', $member_names);
					}
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
			
			//$chatCount = json_decode(Request::getChatMembersCount(['chat_id' => $chat_id]), true)['result'];
			$json = json_decode(Settings::get(['chat_id' => $chat_id]), true);
			$welcome_datas = $json['result']['data'][0];
			$welcome_message = explode("\n\n", $welcome_datas['welcome_message']);
			if (count($member_names) > 0) {
				$new_welcome_message = $welcome_message[0] . "\n\n";
			}
			
			if (count($member_bots) > 0) {
				$new_welcome_message .= $welcome_message[1] . "\n\n";
			}
			
			if (count($member_nounames) > 0) {
				$new_welcome_message .= $welcome_message[2];
			}

//			if (count($member_lnames) > 0) {
//				if ($isKicked['ok'] != false) {
//					$text .=
//						'🚷 < b>Ditendang: </b > (<code > ' . count($member_lnames) . ')</code > ' .
//						"\n" . implode(', ', $member_lnames) . ', Namamu panjang gan!';
//				} else {
//					$text .=
//						' < b>Eksekusi : </b > Mencoba untuk menendang spammer' .
//						"\n < b>Status : </b > " . $isKicked['error_code'] .
//						"\n < b>Result : </b > " . $isKicked['description'];
//				}
//			}
			//$text .= "\n < b>Total : </b > " . $chatCount . 'Anggota';
		}
		
		$data = [
			'chat_id'    => $chat_id,
			'parse_mode' => 'HTML',
		];
		
		$replacement = [
			'full_name'         => $full_name,
			'chat_title'        => $chat_title,
			'namelink'          => $nameLink,
			'new_members_count' => $new_members_count,
			'new_members'       => $new_members,
			'new_bots_count'    => $new_bots_count,
			'new_bots'          => $new_bots,
			'no_username_count' => $no_username_count,
			'no_username'       => $no_username,
			'time_current'      => $time_current,
		];
		
		$text = Kata::resolveVariable(trim($new_welcome_message), $replacement);
		
		$btn_markup = [];
		if ($welcome_datas['welcome_button'] !== '') {
			$btn_data = $welcome_datas['welcome_button'];
			$btn_datas = explode(',', $btn_data);
			foreach ($btn_datas as $key => $val) {
				$btn_row = explode('|', $val);
				$btn_markup[] = ['text' => $btn_row[0], 'url' => $btn_row[1]];
			}
			$data['reply_markup'] = new InlineKeyboard([
				'inline_keyboard' => array_chunk($btn_markup, 2),
			]);
		}
		
		$time2 = Waktu::jedaNew($time);
		$time = "\n\n ⏱ " . $time1 . ' | ⏳ ' . $time2;
		
		$data['text'] = $text . $time;
		
		Request::deleteMessage(['chat_id' => $chat_id, 'message_id' => $welcome_datas['last_welcome_message_id']]);
		
		$r = Request::sendMessage($data);
		
		Settings::save([
			'chat_id'  => $chat_id,
			'property' => 'last_welcome_message_id',
			'value'    => $r->result->message_id,
		]);
		
		return $r;
	}
}
