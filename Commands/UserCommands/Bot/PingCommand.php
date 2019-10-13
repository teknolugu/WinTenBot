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
use WinTenDev\Model\Group;
use WinTenDev\Utils\Time;

class PingCommand extends UserCommand
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

//        if ($hook->pending_update_count > 5) {
//            $pesan = "Pending count lebih dari 5, dan di bersihkan otomatis\n";
//            foreach (sudoer as $sudo) {
//                $data = [
//                    'chat_id' => $sudo,
//                    'text' => $pesan . $time1,
//                    'parse_mode' => 'HTML'
//                ];
//                Request::sendMessage($data);
//            }
////            Request::deleteWebhook();
//            Request::setWebhook(['url' => clean_hook]);
//            Request::setWebhook(['url' => url_hook]);
//        }
		
		$text = '<b>Pong..!!</b>';
		if ($message->getChat()->isPrivateChat()
			&& Group::isSudoer($message->getFrom()->getId())) {
			$hook = \GuzzleHttp\json_decode(Request::getWebhookInfo(), true)['result'];
			$me = Request::getMe();
			$url = $hook['url'] ?? "N/A";
			$pending_update = (int)$hook['pending_update_count'] - 1 ?? 0;
			$last_error_date = Time::formatUnix($hook['last_error_date'] ?? -1);
			$last_error_mssg = $hook['last_error_message'] ?? "N/A";
			$max_connection = $hook['max_connections'] ?? 0;
			$text .=
				"\n<b>Your Access : </b> You is Sudoer!!" .
				"\n<b>Username : </b> @" . $me->getBotUsername() .
				"\n<b>Current Hook : </b> <code>$url</code>" .
//                "\n<b>Clean Hook : </b><code>" . clean_hook . '</code>' .
				"\n<b>Pending Update : </b> $pending_update " .
				"\n<b>Last Error Date : </b> $last_error_date " .
				"\n<b>Last Error Mssg : </b> $last_error_mssg " .
				"\n<b>Max Connection : </b> $max_connection";
		}
		
		return $chatHandler->sendText($text);
	}
}
