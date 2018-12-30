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
    protected $name = 'info';
    protected $description = 'Get information about Me';
    protected $usage = '<info>';
    protected $version = '1.0.0';
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

        $time = $message->getDate();
        $time1 = Waktu::jedaNew($time);

        $text = '🤖 <b>WinTen Beta Bot</b> <code>' . versi . "</code>\n" . descBot;

        if (isBeta) {
            $text .= descBeta;
        }

        $inline_keyboard = new InlineKeyboard([
            ['text' => '👥 WinTen Group', 'url' => 'https://t.me/WinTenGroup'],
            ['text' => '❤ by WinTenDev', 'url' => 'https://t.me/WinTenDev'],
        ], [
            ['text' => '👥 Redmi 5A (Riva) ID', 'url' => 'https://t.me/Redmi5AID'],
            ['text' => '👥 Telegram Bot API', 'url' => 'https://t.me/TgBotID'],
        ], [
            ['text' => '💽 Source code', 'url' => 'https://github.com/WinTenGroup/WinTenBot'],
            ['text' => '🏗 Akmal Projext', 'url' => 'https://t.me/AkmalProjext'],
        ]);

        $time2 = Waktu::jedaNew($time);
        $time = "\n\n ⏱ " . $time1 . ' | ⏳ ' . $time2;

        $data = [
            'chat_id'      => $chat_id,
            'text'         => $text . $time,
            'reply_markup' => $inline_keyboard,
            'parse_mode'   => 'HTML'
        ];

        return Request::sendMessage($data);
    }
}
