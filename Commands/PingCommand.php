<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 04/08/2018
 * Time: 23.33
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Grup;
use App\Waktu;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class PingCommand extends UserCommand
{
    protected $name = 'ping';
    protected $description = 'Get latency Telegram Bot to Servers';
    protected $usage = '<ping>';
    protected $version = '1.0.0';

    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     * @throws \Exception
     */
    public function execute()
    {
        $message = $this->getMessage();

        $chat_id = $message->getChat()->getId();
        $mssg_id = $message->getMessageId();

        $time = $message->getDate();
        $time1 = Waktu::jedaNew($time);

        $hook = json_decode(Request::getWebhookInfo())->result;
        $me = Request::getMe();

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

        $text = "<b>Pong..!!</b>";
        if ($message->getChat()->getType() == "private"
            && Grup::isSudoer($message->getFrom()->getId())) {
            $text .=
                "\n<b>Your Access : </b> You is Sudoer!! " .
                "\n<b>Username : </b> @" . $me->getBotUsername() .
                "\n<b>Current Hook : </b><code>" . $hook->url . '</code>' .
                "\n<b>Clean Hook : </b><code>" . clean_hook . '</code>' .
                "\n<b>Pending Update : </b> " . $hook->pending_update_count .
                "\n<b>Last Error Date : </b> " . Waktu::formatUnix($hook->last_error_date) .
                "\n<b>Last Error Mssg : </b> " . $hook->last_error_message .
                "\n<b>Max Connection : </b> " . $hook->max_connections;
        }

        $time2 = Waktu::jedaNew($time);
        $time = "\n\n ⏱ " . $time1 . " | ⏳ " . $time2;

        $data = [
            'chat_id' => $chat_id,
            'text' => $text . $time,
            'reply_to_message_id' => $mssg_id,
            'parse_mode' => 'HTML'
        ];

        return Request::sendMessage($data);
    }
}
