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
use src\Handlers\ChatHandler;
use src\Model\Fbans;
use src\Model\Group;

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
			"\n\n<b>Warning: </b> Fake reports might make you unable to become an FBan Admin forever!";
		
		$repMssg = $message->getReplyToMessage();
		$data = explode(' ', $message->getText(true));
		
		if (!$chatHandler->isPrivateChat) {
			if (Fbans::isAdminFbans($from_id)) {
				$chatHandler->sendText('🏗 Mempersiapkan..', -1);
				if ($repMssg != '') {
					$user_id = $repMssg->getFrom()->getId();
					$reason_ban = $message->getText(true);
				} elseif ($data[0] != '') {
					$user_id = $data[0];
					$reason_ban = str_replace($user_id, '', $message->getText(true));
				} else {
					$text = "ℹ $federation_name" .
						"\n<code>/fban reason_ban</code> - Reply pesan" .
						"\n<code>/fban user_id reason_ban</code>" .
						"\n\n<b>Warning: </b> Fake reports might make you unable to become an FBan Admin forever!";
					return $chatHandler->editText($text);
				}
				$text = $federation_name . "\n\n";
				
				$banned_by = $message->getFrom()->getId();
				if (Group::isAdmin($user_id, $chat_id)) {
					return $chatHandler->editText($text . 'Admin grup tidak bisa di tambahkan ke daftar FedBan');
				}
				
				$fbans_data = [
					'user_id'     => $user_id,
					'reason_ban'  => $reason_ban ?? '-tidak ada alasan-',
					'banned_by'   => $banned_by,
					'banned_from' => $message->getChat()->getId(),
				];
				
				$chatHandler->editText($text . "Menendang $user_id");
				$chatHandler->kickMember($user_id, true);
				
				$chatHandler->editText($text . "🏗 Menambahkan $user_id");
				$fban = Fbans::saveFBans($fbans_data);
				
				if ($fban) {
					$chatHandler->editText($text . '✍ Menulis ke Cache..');
					Fbans::writeCacheFbans();
					$text = "🧩 $federation_name\n" .
						"\n<b>Banned By: </b> $banned_by" .
						"\n<b>User_ID: </b> $user_id" .
						"\n<b>Reason: </b>" . $fbans_data['$reason_ban'];
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
		
		return $r;
	}
}
