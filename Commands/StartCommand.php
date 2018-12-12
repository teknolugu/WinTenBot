<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 04/08/2018
 * Time: 23.33
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use App\Waktu;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

class StartCommand extends SystemCommand
{
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

        $text = 'Hai, Ada yang bisa saya bantu?';

        $pecah = explode(' ', $message->getText(true));
        switch ($pecah[0]) {
            case 'username':
                return Request::sendDocument([
                    'chat_id'                  => $chat_id,
                    'document'                 => 'CgADBQADIgADzjAhVzAzhd8G8GtBAg',
                    'caption'                  => 'Buka aplikasi Telegram > Settings > Username, lalu isi Username-nya.',
                    'parse_mode'               => 'HTML',
                    'reply_to_message_id'      => $mssg_id,
                    'disable_web_page_preview' => true
                ]);
                break;

            case '1':
                $text = 'Selamat datang di @' . bot_username;
                break;
        }

        $time2 = Waktu::jedaNew($time);
        $time = "\n\n ⏱ " . $time1 . ' | ⏳ ' . $time2;

        $data = [
            'chat_id'             => $chat_id,
            'text'                => $text . $time,
            'reply_to_message_id' => $mssg_id,
            'parse_mode'          => 'HTML'
        ];

        return Request::sendMessage($data);
    }
}
