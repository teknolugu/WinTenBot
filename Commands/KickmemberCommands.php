<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 10.43
 */

namespace Longman\TelegramBot\Commands\UserCommands;


use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class KickmemberCommands extends UserCommand
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
        $from_id = $message->getFrom()->getId();
        $chat_id = $message->getChat()->getId();
        $data = [
            'chat_id' => $chat_id,
            'user_id' => $from_id
        ];

        Request::kickChatMember($data);
    }
}
