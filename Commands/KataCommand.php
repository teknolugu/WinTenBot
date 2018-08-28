<?php
/**
 * Created by PhpStorm.
 * User: azhe403
 * Date: 28/08/18
 * Time: 21:07
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Kata;
use App\Waktu;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Commands\UserCommand;

class KataCommand extends UserCommand
{
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $mssg_id = $message->getMessageId();

        $time = $message->getDate();
        $time = Waktu::jeda($time);

        $pecah = explode(' ', $message->getText());

        switch ($pecah[1]) {
            case 'blok':
                $katas = [
                    'kata' => $pecah[2],
                    'kelas' => "blok",
                    'id_telegram' => $message->getFrom()->getId(),
                    'id_grup' => $chat_id
                ];
                $blok = json_decode(Kata::tambahBadword($katas), true);
                $text = "<b>Diblok : </b>" . $pecah[2] .
                    "\n<b>Status : </b>" . $blok['message'];
                break;

            case 'unblok':
                $text = "ublock";
                break;

            case 'biar':
                $katas = [
                    'kata' => $pecah[2],
                    'kelas' => "biar",
                    'id_telegram' => $message->getFrom()->getId(),
                    'id_grup' => $chat_id
                ];
                $blok = json_decode(Kata::tambahBadword($katas), true);
                $text = "<b>Dibiar : </b>" . $pecah[2] .
                    "\n<b>Status : </b>" . $blok['message'];
                break;

            case 'unbiar':
                $text = "Unbiar";
                break;

            case 'help':
                $text = "<b>Penggunaan /kata</b>" .
                    "\n<code>/kata [command] katamu</code>" .
                    "\n<b>Command : </b><code>blok, unblok, biar, unbiar</code>";
                break;

            default:
                $text = "mau diapain?";
        }

        return Request::sendMessage([
            'chat_id' => $chat_id,
            'text' => $text . $time,
            'reply_to_message_id' => $mssg_id,
            'parse_mode' => 'HTML'
        ]);
    }
}
