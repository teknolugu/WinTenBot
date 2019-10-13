<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 04/08/2018
 * Time: 23.33
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Exception;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use WinTenDev\Handlers\ChatHandler;
use WinTenDev\Model\Logs;

class GreportCommand extends UserCommand
{
	protected $name = 'ping';
	protected $description = 'Get latency Telegram Bot to Servers';
	protected $usage = '<ping>';
	protected $version = '1.0.0';
	
	/**
	 * @return ServerResponse
	 * @throws TelegramException
	 * @throws Exception
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$chatHandler = new ChatHandler($message);
		$repMssg = $message->getReplyToMessage();
		
		$text = '<b>Reply</b> pesan yang akan di Global Report';
		
		if ($repMssg != "") {
			$text = "Pengguna berhasil di laporkan ke Service Center. " .
				"Pesan yang di kirimkan juga di teruskan ke <b>WinTenDev Labs</b> untuk di kaji lebih lanjut. \n";
			
			$text .= "\nID: <code>" . $repMssg->getFrom()->getId() . "</code>";
			
			$text .= "\n\nJika pengguna yang dilaporkan di anggap spamer, silakan di klik tombol <b>Global Ban.</b>";
			
			$keyboard = [
				['text' => 'Global Ban', 'url' => 't.me/wintendev'],
				['text' => 'Kick Member', 'url' => 't.me/wintendev'],
				['text' => 'Delete Message', 'url' => 't.me/wintendev'],
				['text' => 'Close', 'url' => 't.me/wintendev'],
			];
			
			Request::forwardMessage([
				'chat_id'      => log_channel,
				'from_chat_id' => $repMssg->getChat()->getId(),
				'message_id'   => $repMssg->getMessageId(),
			]);
			
			Logs::toChannel($text, $keyboard);
		}
		
		return $chatHandler->sendText($text, '-1', $keyboard);
	}
}
