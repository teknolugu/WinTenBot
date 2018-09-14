<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 10/09/2018
 * Time: 07.55
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Grup;
use App\Waktu;
use App\Kata;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;


class SetCommand extends UserCommand
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
        $mssg_id = $message->getMessageId();
        $from_id = $message->getFrom()->getId();
        $time = $message->getDate();
        $time1 = Waktu::jedaNew($time);

        $isAdmin = Grup::isAdmin($from_id, $chat_id);
        $isSudoer = Grup::isSudoer($from_id);
        if ($isAdmin || $isSudoer) {
            $switch_element = mt_rand(0, 9) < 5 ? 'true' : 'false';
            $inline_keyboard = new InlineKeyboard([
                ['text' => 'inline', 'switch_inline_query' => $switch_element],
                ['text' => 'inline current chat', 'switch_inline_query_current_chat' => $switch_element],
            ], [
                ['text' => 'Welcome', 'callback_data' => 'identifier'],
                ['text' => 'Username', 'url' => 'https://github.com/php-telegram-bot/core'],
                ['text' => 'open url', 'url' => 'https://github.com/php-telegram-bot/core'],
            ], [
                ['text' => 'callback', 'callback_data' => 'identifier'],
                ['text' => 'open url', 'url' => 'https://github.com/php-telegram-bot/core'],
                ['text' => 'open url', 'url' => 'https://github.com/php-telegram-bot/core'],
            ]);

        }

//        else{
//            $text = "Kamu tidak memiliki akses";
//        }

        if ($text != '') {
            $time2 = Waktu::jedaNew($time);
            $time = "\n\n ⏱ " . $time1 . " | ⏳ " . $time2;
        }

        Request::deleteMessage(
            [
                'chat_id' => $chat_id,
                'message_id' => $mssg_id
            ]
        );

        $data = [
            'chat_id' => $from_id,
            'text' => $text . $time,
            'reply_markup' => $inline_keyboard,
            'parse_mode' => 'HTML'
        ];

        return Request::sendMessage($data);
    }
}
