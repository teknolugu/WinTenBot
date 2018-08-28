<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 8/24/2018
 * Time: 4:46 PM
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Waktu;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class InfoCommand extends UserCommand
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

        $time = $message->getDate();
        $time1 = Waktu::jedaNew($time);

        $text = "🤖 <b>WinTen Beta Bot</b> <code>versi " . versi . "</code>\n" .
            "ℹ️ Official Telegram bot based on <b>WinTen API</b>.\n" .
            "for management & utility group";

        $inline_keyboard = new InlineKeyboard([
            ['text' => '👥 WinTen Group', 'url' => 'https://t.me/WinTenGroup'],
            ['text' => 'Made With ❤️ by WinTenDev', 'url' => 'https://t.me/WinTenDev'],
        ], [
            ['text' => '👥 Redmi 5A (Riva) ID', 'url' => 'https://t.me/Redmi5AID'],
            ['text' => '💽 Source code', 'url' => 'http://bit.ly/2PA2bJt'],
        ]);

        $time2 = Waktu::jedaNew($time);
        $time = "\n\n ⏱ " . $time1 . " | ⏳ " . $time2;

        $data = [
            'chat_id' => $chat_id,
            'text' => $text . $time,
            'reply_markup' => $inline_keyboard,
            'parse_mode' => 'HTML'
        ];

        return Request::sendMessage($data);
    }
}
