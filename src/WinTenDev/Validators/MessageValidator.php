<?php

namespace WinTenDev\Validators;

use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use WinTenDev\Handlers\ChatHandler;
use WinTenDev\Model\Fbans;

class MessageValidator
{
	private $message;
	private $chatHandler;
	
	public function __construct(Message $message)
	{
		$this->message = $message;
		$this->chatHandler = new ChatHandler($message);
	}
	
	/**
	 * @return mixed
	 * @throws TelegramException
	 */
	final public function checkForwardedMessage()
	{
		$res = null;
		$f_chat_id = "";
		$msg_id = "";
		$chat_id = "";
		$chat_type  ="";

		if ($this->chatHandler->forwdMsgId != "") {
			if ($this->chatHandler->forwdChatId != "") {
				$f_chat_id = $this->chatHandler->forwdChatId;
				$chat_type = $this->chatHandler->forwdChatType;
			} elseif ($this->message->getForwardFrom() != "") {
				$f_chat_id = $this->message->getForwardFrom()->getId();
			}
			
			$msg_id = $this->chatHandler->message_id;
			$chat_id = $this->chatHandler->chatId;
			$from_id = $this->chatHandler->from_id;
		}
		
		if (Fbans::isBan($f_chat_id)) {
			$text = " Seseorang telah mem-forward pesan dari $chat_type dengan ID <b>$f_chat_id</b> yang terdeteksi spam, " .
				"ane merekeomendasikan Global Ban unuk pengguna ini, " .
				"Untuk aksi cepat nya, silakan klik tombol di bawah ini";
			
			$keyboard = [
				['text' => 'Global Ban', 'callback_data' => "action_instant-fban_{$from_id}_{$msg_id}"],
				['text' => 'Kick Member', 'callback_data' => "action_kick-member_{$from_id}_{$chat_id}"],
				['text' => 'Delete Message', 'callback_data' => "action_delete-message_{$msg_id}_{$chat_id}"],
				['text' => 'Close', 'callback_data' => 'general_close-all_admin'],
			];
			
			$res = $this->chatHandler->sendText($text, '-1', $keyboard);
		}
		return $res;
	}
}
