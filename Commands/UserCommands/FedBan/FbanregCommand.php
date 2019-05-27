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

class FbanregCommand extends UserCommand
{
	protected $name = 'fbanreg';
	protected $description = 'Register to ';
	protected $usage = '/fbanreg';
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
		$this->description = "Register to $federation_name";
		
		$chatHandler->sendText('🤔 Checking permission..', '-1');
		
		if (Group::isAdmin($from_id, $chat_id)) {
			$chatHandler->editText('🏗 Mempersiapkan..');
			if (!$message->getChat()->isPrivateChat()) {
				$reg_fban = [
					'user_id'       => $from_id,
					'username'      => $message->getFrom()->getUsername(),
					'promoted_from' => $chat_id,
				];
				
				$chatHandler->editText("🏗 Sedang meregister $federation_name");
				$result = Fbans::saveAdminFBans($reg_fban);
				if ($result->rowCount() > 0) {
					$chatHandler->editText('✍ Menulis ke cache..');
					Fbans::writeCacheAdminFbans();
					$text = "✅ <b>Kamu</b> berhasil register ke $federation_name";
				} else {
					$text = "✅ <b>Kamu</b> telah register ke $federation_name";
				}
				$text .= '. Terimakasih sudah register. 😊';
			} else {
				$text = "ℹ Registrasi $federation_name hanya via Grup.";
			}
			return $chatHandler->editText($text);
		}
		
		$r = $chatHandler->editText("⚠ Kamu harus admin agar bisa register ke $federation_name");
		
		return $r;
	}
}
