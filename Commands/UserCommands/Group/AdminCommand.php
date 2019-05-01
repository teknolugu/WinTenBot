<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 12.11
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use src\Utils\Words;
use src\Utils\Time;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

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
		$text = '';
		$message = $this->getMessage();
		$chat_id = $message->getChat()->getId();
		$mssg_id = $message->getMessageId();
		
		if(!$message->getChat()->isPrivateChat()) {
			$time = $message->getDate();
			$time1 = Time::jedaNew($time);
			$pecah = explode(' ', $message->getText(true));
			if ($pecah[0] != '') {
				$param = $pecah[0];
				if ($pecah[0][0] != '-') {
					$param = '@' . $pecah[0];
				}
				
				$chat = [
					'chat_id' => $param,
				];
			} else {
				$chat = [
					'chat_id' => $chat_id,
				];
			}
			
			$respon = Request::getChatAdministrators($chat);
			
			$respon = json_decode($respon, true);
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
							$admins .= " 🤖";
						}
						$ngadmins[] = $admins;
					}
					sort($ngadmins);
				}
			}
			
			$ngadmin = '';
			$noAdm = 1;
			$lastAdm = end($ngadmins);
			foreach ($ngadmins as $adminl) {
				if ($adminl != $lastAdm) {
					$ngadmin .= '├ ' . $noAdm . ' . ' . $adminl . "\n";
				} else {
					$ngadmin .= '└ ' . $noAdm . ' . ' . $adminl . "\n";
				}
				$noAdm++;
			}
			
			if ($creator != '') {
				$text .= "👤 <b>Creator</b>\n└ " . $creator;
			}
			
			if ($ngadmin != '') {
				$text .= "\n\n👥️ <b>Administrators: " . count($ngadmins) . "</b>" .
					"\n" . $ngadmin;
			}
			$time2 = Time::jedaNew($time);
			$time = "\n\n ⏱ " . $time1 . " | ⏳ " . $time2;
		}
		
		$data = [
			'chat_id'             => $chat_id,
			'text'                => $text . $time,
			'reply_to_message_id' => $mssg_id,
			'parse_mode'          => 'HTML'
		];
		
		return Request::sendMessage($data);
	}
}
