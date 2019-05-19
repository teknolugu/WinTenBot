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

class UnfbanCommand extends UserCommand
{
	protected $name = 'unfban';
	protected $description = 'Remove account from Federation Ban lists';
	protected $usage = '/unfban';
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
		
		$repMssg = $message->getReplyToMessage();
		$data = explode(' ', $message->getText(true));
		$r = $chatHandler->sendText('🤔 Memeriksa perizinan..', '-1');
		
		if (Fbans::isAdminFbans($from_id)) {
			$chatHandler->editText('🏗 Mempersiapkan..');
			if ($repMssg != '') {
				$user_id = $repMssg->getFrom()->getId();
			} elseif ($data[0] != '') {
				$user_id = $data[0];
			} else {
				$text = "ℹ $federation_name" .
					"\n<code>/unfban</code> (InReply)" .
					"\n<code>/unfban user_id</code> (InMessage)" .
					"\n\n<b>Warning: </b> Fake reports might make you unable to become an FBan Admin forever!";
				return $chatHandler->editText($text);
			}
			
			$banned_by = $message->getFrom()->getId();
			
			$fbans_data = [
				'user_id'   => $user_id,
				'banned_by' => $banned_by,
			];
			
			$text = $federation_name . "\n\n";
			$chatHandler->editText($text . '🗑 Menghapus dari daftar');
			$fban = Fbans::deleteFban($fbans_data);
			if ($fban->rowCount() > 0) {
				$chatHandler->editText('✍ Menulis ke Cache..');
				Fbans::writeCacheFbans();
				$text .= '✅ <b>Pengguna</b> berhasil di hapus.';
			} else {
				$text .= 'ℹ  Hapus pengguna gagal .' .
					"\nKamu tidak bisa meng-unfban akun yang di ban oleh orang lain.";
			}
			$r = $chatHandler->editText($text);
		} else {
			$r = $chatHandler->editText("⚠ Kamu belum terdaftar ke $federation_name");
		}
		
		return $r;
	}
}
