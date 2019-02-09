<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;
use src\Model\Analytic;
use src\Model\Group;
use src\Utils\Words;

/**
 * Generic message command
 */
class GenericmessageCommand extends SystemCommand
{
	protected $name = 'genericmessage';
	protected $description = 'Handle generic message';
	protected $version = '1.0.0';
	
	/**
	 * Execute command
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	public function execute()
	{
		$pesan = $this->getMessage()->getText();
		$message = $this->getMessage();
		$from_id = $message->getFrom()->getId();
		$from_first_name = $message->getFrom()->getFirstName();
		$from_last_name = $message->getFrom()->getLastName();
		$from_username = $message->getFrom()->getUsername();
		$chat_id = $message->getChat()->getId();
		$chat_username = $message->getChat()->getUsername();
		$chat_type = $message->getChat()->getType();
		$chat_title = $message->getChat()->getTitle();
		$repMsg = $this->getMessage()->getReplyToMessage();
		
		$kata = strtolower($pesan);
		$pesanCmd = explode(' ', strtolower($pesan))[0];
		
		// Pindai kata
		if (Words::isBadword($kata)) {
			$data = [
				'chat_id'    => $chat_id,
				'message_id' => $message->getMessageId(),
			];
			
			Request::deleteMessage($data);
		}
		
		// Perika apakah Aku harus keluar grup?
		if (isRestricted
			&& !$message->getChat()->isPrivateChat()
			&& Group::isMustLeft($message->getChat()->getId())) {
			$text = 'Sepertinya saya salah alamat. Saya pamit dulu..' .
				"\nGunakan @WinTenBot";
			Request::sendMessage([
				'chat_id'    => $chat_id,
				'text'       => $text,
				'parse_mode' => 'HTML',
			]);
			Request::leaveChat(['chat_id' => $chat_id]);
		}
		
		// Save some data into DB, for analytic purposed only.
		Analytic::logChat([
			'from_id'         => $from_id,
			'from_first_name' => $from_first_name,
			'from_last_name'  => $from_last_name,
			'from_username'   => $from_username,
			'chat_id'         => $chat_id,
			'chat_username'   => $chat_username,
			'chat_type'       => $chat_type,
			'chat_title'      => $chat_title,
		]);
		
		// Command Aliases
		switch ($pesanCmd) {
			case 'ping':
				return $this->telegram->executeCommand('ping');
				break;
			case 'notes':
				return $this->telegram->executeCommand('tags');
				break;
			case '@admin':
				return $this->telegram->executeCommand('report');
				break;
			case Words::cekKandungan($pesan, '#'):
				return $this->telegram->executeCommand('get');
				break;
		}
		
		//Cek Makasih
		$makasih = Words::cekKata($kata, thanks);
		if ($makasih) {
			$text = 'Sama-sama, senang bisa membantu gan...';
			Request::sendMessage([
				'chat_id'             => $chat_id,
				'text'                => $text,
				'reply_to_message_id' => $message->getMessageId(),
				'parse_mode'          => 'HTML',
			]);
		}
		
		// Chatting
		$chat = '';
		switch (true) {
			case Words::cekKata($kata, 'gan'):
				$chat = 'ya gan, gimana';
				break;
			case Words::cekKata($kata, 'mau tanya'):
				$chat = 'Langsung aja tanya gan';
				break;
			
			default:
				break;
		}
		
		Request::sendMessage([
			'chat_id'             => $chat_id,
			'text'                => $chat,
			'reply_to_message_id' => $message->getMessageId(),
			'parse_mode'          => 'HTML',
		]);
		
		if ($repMsg !== null) {
			if ($message->getChat()->getType() != "private") {
				$text = "<a href='tg://user?id=" . $from_id . "'>" . $from_first_name . '</a>' . ' mereply ' .
					"<a href='https://t.me/" . $chat_username . '/' .
					$message->getMessageId() . "'>pesan kamu" . '</a>' . ' di grup <b>' . $chat_title . '</b>';
				$text .= "\n" . $message->getText();
				$data = [
					'chat_id'                  => $repMsg->getFrom()->getId(),
					'text'                     => $text,
					'parse_mode'               => 'HTML',
					'disable_web_page_preview' => true,
				];
				
				return Request::sendMessage($data);
			} else {
				$chat_id = $repMsg->getCaptionEntities()[3]->getUrl();
				$chat_id = str_replace("tg://user?id=", "", $chat_id);
				
				$data = [
					'chat_id'                  => $chat_id,
					'text'                     => "lorem",
					'parse_mode'               => 'HTML',
					'disable_web_page_preview' => true,
				];
				
				return Request::sendMessage($data);
			}
		}
		
		$pinned_message = $message->getPinnedMessage()->getMessageId();
		if (isset($pinned_message)) {
			return $this->telegram->executeCommand('pinnedmessage');
		}
	}
}

