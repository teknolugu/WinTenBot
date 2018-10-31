<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 8/23/2018
 * Time: 10:45 AM
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Waktu;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class TagsCommand extends UserCommand
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

        $url = winten_api . "tag/$chat_id?api_token=" . winten_key;
        $json = file_get_contents($url);
        $datas = json_decode($json, true);
        if (count($datas['message']) > 0) {
            $hit = count($datas['message']);
            $text = "#️⃣  <b>$hit Tags</b>\n-------\n";
            foreach ($datas['message'] as $data) {
                $text .= " #" . $data['tag'];
            }
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
