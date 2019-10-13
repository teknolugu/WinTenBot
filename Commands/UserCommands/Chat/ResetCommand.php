<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use WinTenDev\Handlers\ChatHandler;

class ResetCommand extends UserCommand
{
	
	/**
	 * Execute command
	 *
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function execute()
	{
		// TODO: Implement execute() method.
		$message = $this->getMessage();
		$handler = new ChatHandler($message);
		
		$pecah = explode(",", $message->getText(true));
		
		$canReset = false;
		if ($handler->isAdmin()
			|| $handler->isSudoer()
			|| $handler->isPrivateChat) {
			$canReset = true;
		}
		
		if ($canReset) {
			$handler->sendText("Mempersiapkan..");
			$btn_markup = [
				['text' => 'Reset cache #s', 'callback_data' => 'cache_tags'],
				['text' => 'Reset Cache ⚙', 'callback_data' => 'cache_setting'],
//				['text' => 'Cache All', 'callback_data' => 'cache_all'],
				['text' => '⚠ Soft Reset ⚙', 'callback_data' => 'cache_all'],
				['text' => '⚠ Hard Reset ⚙', 'callback_data' => 'cache_all'],
			];
//			$dir = botData .'cache-json/'.$handler->getChatId();
//			if(file_exists($dir)){
//				$handler->editText("Sedang mereset Cache..");
//				Folder::deleteDir($dir);
//			}else{
//				return $handler->editText("Cache sudah di bersihkan untuk Obrolan ini.",null, $btn_markup);
			$text = "⚒ <b>Action Center</b> for <b>{$handler->chatTitle}</b>.".
				"\nJika Tags, Settings, etc. tidak berjalan dengan baik, Cobalah Reset. " .
				"Jika Anda bingung ketikkan perintah /help";
			$res = $handler->editText($text, null, $btn_markup);
//			}
//			$res = $handler->editText("Cache berhasil di reset untuk Obrolan ini.");
		}
		
		return $res;
	}
}
