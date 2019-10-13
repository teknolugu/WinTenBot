<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 04/08/2018
 * Time: 23.33
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use WinTenDev\Handlers\ChatHandler;
use WinTenDev\Model\Fbans;
use WinTenDev\Model\Group;

class FbanCommand extends UserCommand
{
	protected $name = 'fban';
	protected $description = 'Lets ban federation';
	protected $usage = '/fban';
	protected $version = '1.0.0';
	
	/**
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$from_id = $message->getFrom()->getId();
		$chat_id = $message->getChat()->getId();
		$chatHandler = new ChatHandler($message);
		$federation_name = federation_name;
		$not_registered = $text = '⚠ Kamu belum teregistrasi ke ' . federation_name .
			"\nKamu dapat register dengan <code>/fbanreg</code>" .
			"\n\n<b>Peringatan: </b> Laporan palsu mungkin dapat membuat Anda tidak dapat menjadi Admin FBan " .
			"atau Grub Anda akan di batasi selamanya!";
		
		$repMssg = $message->getReplyToMessage();
		$data = explode(' ', $message->getText(true));
		
		if (!$chatHandler->isPrivateChat) {
			if (Fbans::isAdminFbans($from_id)) {
				$text = '🏗 Mempersiapkan..';
				$chatHandler->sendText($text, -1);
				$chatHandler->deleteMessage();
				if ($repMssg != '') {
					$user_id = $repMssg->getFrom()->getId();
					$reason_ban = $message->getText(true);
					$spamId = $repMssg->getMessageId();
				} elseif ($data[0] != '') {
					$user_id = $data[0];
					$reason_ban = str_replace($user_id, '', $message->getText(true));
				} else {
					$text = "ℹ $federation_name" .
						"\n<code>/fban reason_ban</code> - Reply pesan" .
						"\n<code>/fban user_id reason_ban</code>" .
						"\n\n<b>Peringatan: </b> Laporan palsu mungkin dapat membuat Anda tidak dapat menjadi " .
						"Admin FBan atau Grub Anda akan di batasi selamanya!";
					return $chatHandler->editText($text);
				}
				$text = $federation_name . "\n";
				
				$banned_by = $message->getFrom()->getId();
				if (Group::isAdmin($user_id, $chat_id)) {
					return $chatHandler->editText($text . 'Admin grup tidak bisa di tambahkan ke daftar FedBan');
				}
				
				if (!is_numeric($user_id)) {
					return $chatHandler->editText("User ID untuk FBan hanya berupa angka");
				}
				
				if ($reason_ban == '') $reason_ban = "Tidak ada alasan";
				
				$fbans_data = [
					'user_id'     => $user_id,
					'reason_ban'  => $reason_ban,
					'banned_by'   => $banned_by,
					'banned_from' => $message->getChat()->getId(),
				];
				
				$text .= "\nMenendang $user_id";
				$chatHandler->editText($text);
				$chatHandler->kickMember($user_id, true);
				
				$text .= "\n🏗 Menambahkan $user_id";
				$chatHandler->editText($text);
				$fban = Fbans::saveFBans($fbans_data);
				
				if ($fban) {
					$text .= "\n✍ Menulis ke Cache..";
					$chatHandler->editText($text);
					$writeFban = Fbans::writeCacheFbans();
					$text = "🧩 $federation_name\n" .
						"\n<b>Banned By: </b> $banned_by" .
						"\n<b>User_ID: </b> <a href='tg://user?id=$user_id'>$user_id</a>" .
						"\n<b>Reason: </b>" . $reason_ban;
//					if($writeFban > 0){
					
					if ($spamId != '') {
						Request::forwardMessage([
							'chat_id'      => log_channel,
							'from_chat_id' => $repMssg->getChat()->getId(),
							'message_id'   => $repMssg->getMessageId(),
						]);
						$chatHandler->deleteMessage($repMssg->getMessageId());
					}
					$log = $text .
						"\nWrite: " . $writeFban;
					$chatHandler->logToChannel($log);
//					}
				} else {
					$text = "$federation_name\n\nℹ  <b>User_ID</b> sudah di tambahkan ke daftar FedBan";
				}
				$r = $chatHandler->editText($text);
			} else {
				$r = $chatHandler->sendText($not_registered);
			}
		} else {
			$text = "$federation_name\n\n" . '⚠ Perintah /fban hanya di lakukan di grup.';
			$r = $chatHandler->sendText($text);
		}
		$chatHandler->deleteMessage($chatHandler->getSendedMessageId(), 3);
		
		return $r;
	}
}
