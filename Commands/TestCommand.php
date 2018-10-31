<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 07/09/2018
 * Time: 19.02
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Terjemah;
use App\Waktu;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;


class TestCommand extends UserCommand
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
        $user_id = $message->getFrom()->getId();
        $mssg_id = $message->getMessageId();

        $time = $message->getDate();
        $time = Waktu::jeda($time);
        if ($this->telegram->isAdmin($user_id)) {
            $text = "<b>Pong..!!</b>";
        } else {
            $text = "Tis";
        }

        $data = [
            'chat_id' => $chat_id,
            'text' => $text . $time,
            'reply_to_message_id' => $mssg_id,
            'parse_mode' => 'HTML'
        ];

        return Request::sendMessage($data);
    }
}
