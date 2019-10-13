<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 14/09/2018
 * Time: 16.12
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use WinTenDev\Handlers\ChatHandler;
use WinTenDev\Model\Group;
use WinTenDev\Utils\Time;

class ReportCommand extends UserCommand
{
    protected $name = 'report';
    protected $description = 'Report message to all Admin';
    protected $usage = '/report';
    protected $version = '1.0.0';

    /**
     * Execute command
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $mHandler = new ChatHandler($message);
        $chat_id = $message->getChat()->getId();
        $chat_user = $message->getChat()->getUsername();
        $mssg_id = $message->getMessageId();
        $repMssg = $message->getReplyToMessage();
        $pecah = explode(' ', $message->getText());

        $time = $message->getDate();
        $time1 = Time::jedaNew($time);

        if (!$message->getChat()->isPrivateChat()) {
            if ($repMssg != null) {
                $idAdmins = Group::idAdmins($chat_id);

                $text = '🔄 Pesan sedang di laporkan kesemua admin..';

//	        $time2 = Time::jedaNew($time);
//            $time = "\n\n ⏱ " . $time1 . ' | ⏳ ' . $time2;
//
//            $data = [
//                'chat_id' => $chat_id,
//                'text' => $text . $time,
//                'parse_mode' => 'HTML'
//            ];
//
////            Request::sendMessage($data);
                $mHandler->deleteMessage();
                $mHandler->sendText($text, '-1');
                $mentionAdmin = [];
                $count_admins = count($idAdmins);
                $progress = 1;
                foreach ($idAdmins as $idAdmin) {
                    $fullname = trim($message->getFrom()->getFirstName() . ' ' . $message->getFrom()->getLastName());
                    $alasan = $message->getText(true) ?? "<code>Tidak ada</code>";
                    $pesan = '<b>🛎 Ada pesan laporan</b>' .
                        "\n👤 <b>Pelapor : </b>" . $fullname .
                        "\n👥 <b>Grup : </b>" . $message->getChat()->getTitle() .
                        "\n🗒 <b>Alasan : </b>" . $alasan .
                        "\n\n<i>Silakan di tindak lanjut</i>\n";

                    $keyboard = [
                        ['text' => '👥 Ke pesan', 'url' => 'https://t.me/c/' . str_replace('-100', '', $chat_id) . '/' . $repMssg->getMessageId()],
                        ['text' => '❌ Delete Message', 'callback_data' => 'action_delete-message_' . $repMssg->getMessageId() . '_' . $chat_id],
                        ['text' => '💤 Kick Member', 'callback_data' => 'action_kick-member_' . $repMssg->getFrom()->getId() . '_' . $chat_id],
                        ['text' => '❤ Ban Member', 'callback_data' => 'action_ban-member_' . $repMssg->getFrom()->getId() . '_' . $chat_id]
                    ];

                    $mHandler->editText("🔄 Melaporkan ke $idAdmin" .
                        "\n $progress of $count_admins ");

                    $time2 = Time::jedaNew($time);
                    $time = "\n\n ⏱ " . $time1 . ' | ⏳ ' . $time2;

//                $mentionAdmin[] = "<a href='tg://user?id=" . $idAdmin . "'>⁣</a>";
//                $m = implode(' ', $mentionAdmin);
                    Request::forwardMessage([
                        'chat_id' => $idAdmin,
                        'from_chat_id' => $chat_id,
                        'message_id' => $repMssg->getMessageId()
                    ]);

                    Request::sendMessage([
                        'chat_id' => $idAdmin,
                        'text' => $pesan . $time1,
                        'reply_markup' => new InlineKeyboard([
                            'inline_keyboard' => array_chunk($keyboard, 2),
                        ]),
                        'parse_mode' => 'HTML'
                    ]);
                    $progress++;
                }
                $r = $mHandler->editText("✅ Selesai melaporkan! ke $count_admins Admins");
                sleep(3);
                $mHandler->deleteMessage($r->result->message_id);
            } else {
                $r = $mHandler->sendText("ℹ <b>Reply</b> pesan untuk melaporkan.");
            }
        } else {
            $r = $mHandler->sendText("ℹ Perintah ini hanya untuk Grup");
        }

//        return Request::deleteMessage([
//            'chat_id' => $chat_id, 'message_id' => $mssg_id
//        ]);

        return $r;
    }
}
