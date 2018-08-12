<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 07/08/2018
 * Time: 21.30
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Waktu;
use App\Kata;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class LinkssCommand extends UserCommand
{
    protected $version = '1.0.0';

    /**
     * Execute command
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $link = 'https://winten.tk';
        $text = '';
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $mssg_id = $message->getMessageId();
        $from_id = $message->getFrom()->getId();
        $atext = explode(' ', $message->getText());
        $repMssg = $message->getReplyToMessage();

        $time = $message->getDate();
        $time = Waktu::jeda($time);

        if ($repMssg != null) {
            $url = Kata::extrlink($repMssg->getText(), $atext[1] ?? '0');
            if ($url != '') {
                $link = $url;
            } else {
                $text = "Tidak ada URL di temukan \n";
            }
        } else if ($atext[1] != '') {
            $link = $atext[1];
        }

        $base_url = 'https://api.thumbnail.ws/api/' . thumbws_token . '/thumbnail/get?url=' . $link . '/&width=1280';
        $img = $this->telegram->getDownloadPath() . '/pictures/' . $chat_id . $from_id . $mssg_id . 'azhekun.jpg';
        copy($base_url, $img);
        $text .= 'Ini SS dari ' . $link;

        return Request::sendPhoto([
            'chat_id' => $chat_id,
            'photo' => $base_url,
            'caption' => $text . $time,
            'reply_to_message_id' => $mssg_id,
            'parse_mode' => 'HTML'
        ]);

    }
}
