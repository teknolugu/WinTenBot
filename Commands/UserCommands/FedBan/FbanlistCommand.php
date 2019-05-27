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
use src\Utils\Converters;

class FbanlistCommand extends UserCommand
{
	protected $name = 'fbanlist';
	protected $description = 'Lets ban federation';
	protected $usage = '/fbanlist';
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
		
		$data = explode(' ', $message->getText(true));
		$r = $chatHandler->sendText('🤔 Memeriksa izin..', '-1');
		if (Group::isSudoer($from_id)) {
			if ($data[0] == 'admin') {
				$chatHandler->editText('🏗 Mempersiapkan data..');
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
				$chatHandler->editText('✍ Menulis ke cache..');
				Fbans::writeCacheAdminFbans();
				$text = "$federation_name \n\n📜 <b>Admin Fbans Lists</b>: " . $countAdmin .
					"\n-------------------------------------------------\n" . trim($lists);
			} else {
				$chatHandler->editText('🏗 Mengumpulkan data..');
				$lists = '';
				$fbans = Fbans::getAll();
				$countFBans = count($fbans);
				if ($countFBans > 0) {
					foreach ($fbans as $fban) {
						$lists .= $fban['user_id'] . ' by ' .
							$fban['banned_by'] . ' | ' .
							$fban['banned_from'] . "\n";
					}
				} else {
					$lists = 'No FBans';
				}
				$chatHandler->editText('✍ Menulis ke cache..');
				Fbans::writeCacheFbans();
				$text = "$federation_name \n\n📜 <b>Fbans Lists</b>: " . $countFBans .
					"\n-------------------------------------------------\n" . trim($lists);
			}
			$r = $chatHandler->editText($text);
		} else {
			$chatHandler->editText('Untuk saat ini belum bisa, tunggu ya.');
		}
		return $r;
	}
}
