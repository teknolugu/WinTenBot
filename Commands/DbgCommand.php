<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 12/08/2018
 * Time: 17.02
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Waktu;
use App\Kata;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class DbgCommand extends UserCommand
{

    /**
     * Execute command
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $repMssg = $message->getReplyToMessage();
        $time = $message->getDate();

        if ($repMssg != null) {

            $text = "<b>Debug</b>\n" .
                "<code>" .
                json_encode($repMssg, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) .
                "</code>";
        } else {
            $text = "Reply yang akan di debug";
        }

        $time = Waktu::jeda($time);
        $data = [
            'chat_id' => $chat_id,
            'text' => $text . $time,
            'parse_mode' => 'HTML'
        ];

        return Request::sendMessage($data);
    }
}
