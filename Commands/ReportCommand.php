<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 14/09/2018
 * Time: 16.12
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Grup;
use App\Waktu;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class ReportCommand extends UserCommand
{
    protected $name = 'report';
    protected $description = 'Report message to all Admin';
    protected $usage = '/report';
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
        $chat_user = $message->getChat()->getUsername();
        $mssg_id = $message->getMessageId();
        $repMssg = $message->getReplyToMessage();
        $pecah = explode(' ', $message->getText());

        $time = $message->getDate();
        $time1 = Waktu::jedaNew($time);

        if ($repMssg != null) {
            $idAdmins = Grup::idAdmins($chat_id);

            $text = 'Pesan sedang di laporkan kesemua admin..';

            $time2 = Waktu::jedaNew($time);
            $time = "\n\n ⏱ " . $time1 . ' | ⏳ ' . $time2;

            $data = [
                'chat_id' => $chat_id,
                'text' => $text . $time,
                'parse_mode' => 'HTML'
            ];

            Request::sendMessage($data);

            foreach ($idAdmins as $idAdmin) {
                $fullname = trim($message->getFrom()->getFirstName() . ' ' . $message->getFrom()->getLastName());
                $pesan = '<b>🛎 Ada pesan laporan</b>' .
                    "\n👤 <b>Pelaport : </b>" . $fullname .
                    "\n👥 <b> Grup : </b>" . $message->getChat()->getTitle() .
                    "\n🗒 <b>Alasan : </b>" . str_replace($pecah[0], '', $message->getText()) .
                    "\n\n<i>Silakan di tindak lanjut</i>\n";

                $inline_keyboard = new InlineKeyboard([
                    ['text' => '👥 Ke pesan', 'url' => 'https://t.me/' . $chat_user . '/' . $repMssg->getMessageId()]
                ]);

                $time2 = Waktu::jedaNew($time);
                $time = "\n\n ⏱ " . $time1 . ' | ⏳ ' . $time2;

                Request::forwardMessage([
                    'chat_id' => $idAdmin,
                    'from_chat_id' => $chat_id,
                    'message_id' => $repMssg->getMessageId()
                ]);

                Request::sendMessage([
                    'chat_id' => $idAdmin,
                    'text' => $pesan . $time1,
                    'reply_markup' => $inline_keyboard,
                    'parse_mode' => 'HTML'
                ]);
            }
        }

        return Request::deleteMessage([
            'chat_id' => $chat_id, 'message_id' => $mssg_id
        ]);
    }
}
