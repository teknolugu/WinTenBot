<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 07/09/2018
 * Time: 19.02
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use WinTenDev\Handlers\ChatHandler;
use WinTenDev\Model\Settings;

class ResetsettingCommand extends UserCommand
{
	protected $name = 'resetsetting';
	protected $description = 'Soft reset setting (-h) for hardreset';
	protected $usage = '/resetsetting';
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
		$chatHandler = new ChatHandler($message);
		$chat_id = $message->getChat()->getId();
		$pecah = explode(' ', $message->getText(true));
		
		if ($message->getChat()->getType() != 'private') {
			if ($chatHandler->isAdmin()) {
				$chatHandler->deleteMessage();
				if ($pecah[0] == '-h') {
					$chatHandler->sendText('Mengatur ulang (hard reset)', '-1');
					$reset = Settings::softReset($chat_id, true);
					$type = 'hard';
				} else {
					$chatHandler->sendText('Mengatur ulang (soft reset)', '-1');
					$reset = Settings::softReset($chat_id);
					$type = 'soft';
				}
				if ($reset->rowCount() > 0) {
					$text = "ℹ Pengaturan grup berhasil di $type reset";
				} else {
					$text = "ℹ Tidak ada yang harus di $type reset. Semuanya OK.";
				}
				$text .= "\n\nUntuk melakukan hard reset dengan (-h)." .
					"\n<b>Peringatan:</b> Hard reset akan mengatur ulang pengaturan grup ke bawaan" .
					' tanpa menghapus rules, welcome message dan welcome button.';
				$r = $chatHandler->editText($text);
//				$chatHandler->deleteMessage($chatHandler->getSendedMessageId(), 2);
			}
		} else {
			$r = $chatHandler->sendText('Perintah ini hanya untuk grup');
		}
		
		return $r;
	}
}
