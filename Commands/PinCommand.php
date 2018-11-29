<?php
/**
 * Created by IntelliJ IDEA.
 * User: Azhe
 * Date: 11/23/2018
 * Time: 5:27 PM
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Grup;
use App\Waktu;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class PinCommand extends UserCommand
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
        $from_id = $message->getFrom()->getId();
        $repMssg = $message->getReplyToMessage();

        $time = $message->getDate();
        $time1 = Waktu::jedaNew($time);
        $pecah = explode(' ', $message->getText());

        $isAdmin = Grup::isAdmin($from_id, $chat_id);
        $isSudoer = Grup::isSudoer($from_id);
        if ($isAdmin || $isSudoer) {
            if ($repMssg != null) {
                $pin_data = [
                    'chat_id' => $chat_id,
                    'message_id' => $repMssg->getMessageId()
                ];
                if($pecah[1] == '-s'){
                    $pin_data['disable_notification'] = true;
                }
                Request::deleteMessage([
                    'chat_id' => $chat_id,
                    'message_id' => $message->getMessageId()
                ]);
                Request::pinChatMessage($pin_data);
            } else {
                if($pecah[1] == '-u'){
                    Request::unpinChatMessage([
                        'chat_id' => $chat_id
                    ]);
                    Request::deleteMessage([
                        'chat_id' => $chat_id,
                        'message_id' => $message->getMessageId()
                    ]);
                    $text = '✅ <i>Pesan di sematkan dilepas</i>';
                }else {
                    $text = '📛 <i>Reply pesan yang akan di Pin</i>';
                }
            }
        }else{
            $text = '📛 <i>Anda tidak punya hak akses</i>';
        }

        $time2 = Waktu::jedaNew($time);
        $time = "\n\n ⏱ " . $time1 . ' | ⏳ ' . $time2;

        $data = [
            'chat_id' => $chat_id,
            'parse_mode' => 'HTML'
        ];

        if($text != '') {
            $data['text'] = $text . $time;
        }

        return Request::sendMessage($data);
    }
}
