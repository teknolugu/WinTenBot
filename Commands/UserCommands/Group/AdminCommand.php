<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 12.11
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use src\Handlers\ChatHandler;
use src\Utils\Words;

class AdminCommand extends UserCommand
{
	protected $name = 'admin';
	protected $description = 'Get list all Admins group and current bot (if admin)';
	protected $usage = '<admin>';
	protected $version = '1.0.0';
	
	/**
	 * Execute command
	 *
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$chat_id = $message->getChat()->getId();
		$chatHandler = new ChatHandler($message);
		
		if (!$message->getChat()->isPrivateChat()) {
			$pecah = explode(' ', $message->getText(true));
			if (Words::isContain($pecah[0], '-')) {
				$chat_id = $pecah[0];
			} elseif ($pecah[0] != '') {
				$chat_id = '@' . str_replace('@', '', $pecah[0]);
			}
			
			$chat = [
				'chat_id' => $chat_id,
			];
			
			$chatHandler->sendText("🔄 Loading $chat_id..");
			$respon = Request::getChatAdministrators($chat);
			
			$respon = \GuzzleHttp\json_decode($respon, true);
			$result = $respon['result'];
			$ngadmins = [];
			if (count($result) > 0) {
				foreach ($result as $admin) {
					$fullname = trim($admin['user']['first_name'] . ' ' . $admin['user']['last_name']);
					$fullname = Words::substrteks($fullname, 30);
					$fullname = htmlspecialchars($fullname);
					if ($fullname == null) {
						$fullname = 'Akun terhapus';
					}
					if ($admin['status'] == 'creator') {
						$creator = "<a href='tg://user?id=" . $admin['user']['id'] . "'>" . $fullname . '</a>';
					} else {
						$admins = "<a href='tg://user?id=" . $admin['user']['id'] . "'>" . $fullname . '</a>';
						if ($admin['user']['is_bot']) {
							$admins .= ' 🤖';
						}
						$ngadmins[] = $admins;
					}
				}
				sort($ngadmins);
				$ngadmin = '';
				$noAdm = 1;
				$lastAdm = end($ngadmins);
				foreach ($ngadmins as $adminl) {
					if ($adminl != $lastAdm) {
						$ngadmin .= '├ ' . $noAdm . ' . ' . $adminl . "\n";
					} else {
						$ngadmin .= '└ ' . $noAdm . ' . ' . $adminl;
					}
					$noAdm++;
				}
				
				$text = "🧩 <b>ID/Username:</b> $chat_id\n\n";
				
				if ($creator != '') {
					$text .= "👤 <b>Creator</b>\n└ " . $creator;
				}
				
				$text = trim($text);
				
				if ($ngadmin != '') {
					$text .= "\n\n👥️ <b>Administrators: " . count($ngadmins) . '</b>' .
						"\n" . $ngadmin;
				}
			} else {
				$text = "🤔 Tidak ada hasil, mungkin parameter salah.\nKalau chat_id depanya harus ada tanda -";
			}
		} else {
			return $chatHandler->sendText('Perintah /admin hanya di jalankan di grup.');
		}
		
		return $chatHandler->editText($text);
	}
}
