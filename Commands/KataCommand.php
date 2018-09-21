<?php
/**
 * Created by PhpStorm.
 * User: azhe403
 * Date: 28/08/18
 * Time: 21:07
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Grup;
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
        $from_id = $message->getFrom()->getId();

        $time = $message->getDate();
        $time = Waktu::jeda($time);

        $pecah = explode(' ', $message->getText());
        $isSudoer = Grup::isSudoer($from_id);
        if ($isSudoer) {
            switch ($pecah[1]) {
                case 'blok':
                    $katas = [
                        'kata' => $pecah[2],
                        'kelas' => 'blok',
                        'id_telegram' => $message->getFrom()->getId(),
                        'id_grup' => $chat_id
                    ];
                    $blok = json_decode(Kata::tambahKata($katas), true);
                    $text = '<b>Diblok : </b>' . $pecah[2] .
                        "\n<b>Status : </b>" . $blok['message'];
                    break;

                case 'biar':
                    $katas = [
                        'kata' => $pecah[2],
                        'kelas' => 'biar',
                        'id_telegram' => $message->getFrom()->getId(),
                        'id_grup' => $chat_id
                    ];
                    $blok = json_decode(Kata::tambahKata($katas), true);
                    $text = '<b>Dibiar : </b>' . $pecah[2] .
                        "\n<b>Status : </b>" . $blok['message'];
                    break;

                case 'del':
                    $del = json_decode(Kata::hapusKata($pecah[2]), true);
                    $text = '<b>Hapus : </b>' . $pecah[2] .
                        "\n<b>Status : </b>" . $del['message'];
                    break;

                default:
                    $text = '<b>Penggunaan /kata</b>' .
                        "\n<code>/kata [command] katamu</code>" .
                        "\n<b>Command : </b><code>blok, biar, del</code>";
            }
        }

        Kata::simpanJson();

        return Request::sendMessage([
            'chat_id' => $chat_id,
            'text' => $text . $time,
            'reply_to_message_id' => $mssg_id,
            'parse_mode' => 'HTML'
        ]);
    }
}
