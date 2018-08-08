<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 03.55
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use App\Waktu\Waktu;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;
use GoogleTranslate\GoogleTranslate;

class NewchatmembersCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'newchatmembers';
    /**
     * @var string
     */
    protected $description = 'New Chat Members';
    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $members = $message->getNewChatMembers();
        $chat_tit = $message->getChat()->getTitle();
        $chat_uname = $message->getChat()->getUsername();
        $isKicked = false;
//        $pinned_msg = $message->getPinnedMessage()->;

        $time = $message->getDate();
        $time = Waktu::jeda($time);

        if ($message->botAddedInChat() || $message->getNewChatMembers()) {
            $member_names = [];
            $member_nounames = [];
            $member_bots = [];
            $member_lnames = [];

            $data = [
                'chat_id' => $chat_id,
                'message_id' => $message->getMessageId()
            ];

            Request::deleteMessage($data);
            foreach ($members as $member) {
                $nameLen = strlen($member->getFirstName() . ' ' . $member->getLastName());
                if ($nameLen < 140) {
                    if ($member->getUsername() === null) {
                        $member_nounames[] = "<a href='tg://user?id=" . $member->getId() . "'>"
                            . $member->getFirstName() . '</a>';
                    }

                    if ($member->getIsBot() === true) {
                        $member_bots [] = "<a href='tg://user?id=" . $member->getId() . "'>"
                            . $member->getFirstName() . '</a> 🤖';
                    }

                    $member_names[] = "<a href='tg://user?id=" . $member->getId() . "'>"
                        . $member->getFirstName() . '</a>';
                } else {
                    $member_lnames [] = "<a href='tg://user?id=" . $member->getId() . "'>"
                        . $member->getFirstName() . '</a>';
                    $data = [
                        'chat_id' => $chat_id,
                        'user_id' => $member->getId()
                    ];
                    $isKicked = Request::kickChatMember($data);
                    $isKicked = json_decode($isKicked, true);
                    Request::unbanChatMember($data);

                    $data = [
                        'chat_id' => $chat_id,
                        'message_id' => $message->getMessageId()
                    ];

                    Request::deleteMessage($data);
                }

            }

            if (count($member_names) > 0) {
                $text =
                    "<b>👥 Anggota baru: </b> (<code>" . count($member_names) . ")</code>" .
                    "\nHi " . implode(', ', $member_names) .
                    "\nSelamat datang di kontrakan <b>" . $chat_tit . '</b>';
            }

            if (count($member_bots) > 0) {
                $text .=
                    "\n\n<b>🤖 Bot baru: </b> (<code>" . count($member_bots) . ")</code>" .
                    "\nHi " . implode(', ', $member_bots) .
                    "\nSiapa yang menambahkan kamu?";
            }

            if (count($member_nounames) > 0) {
                $text .=
                    "\n\n<b>⚠ Tanpa Username: </b> (<code>" . count($member_nounames) . ")</code>" .
                    "\n" . implode(', ', $member_nounames) . ", Tolong pasang username" .
                    "\n<i>Buka aplikasi Telegram > Settings > Username, lalu isi Username-nya.</i>";
            }

            if (count($member_lnames) > 0) {
                if ($isKicked['ok'] != false) {
                    $text .=
                        "🚷 <b>Ditendang: </b> (<code>" . count($member_lnames) . ")</code>" .
                        "\n" . implode(', ', $member_lnames) . ", Namamu panjang gan!";
                } else {
                    $text .=
                        "<b>Eksekusi : </b> Mencoba untuk menendang spammer" .
                        "\n<b>Status : </b>" . $isKicked['error_code'] .
                        "\n<b>Result : </b>" . $isKicked['description'] . "";
                }
            }
        }

        $in_keyboard = new InlineKeyboard([
            ['text' => '📢 Channel', 'url' => 'https://t.me/WinTenChannel'],
            ['text' => '📌 Pinned', 'url' => 'https://t.me/' . $chat_uname . '/'],
            ['text' => '🌐 Site', 'url' => 'https://winten.tk']
        ]);

        $data = [
            'chat_id' => $chat_id,
            'text' => $text . $time,
            'parse_mode' => 'HTML',
            'reply_markup' => $in_keyboard
        ];
        if ($text !== null) {
            return Request::sendMessage($data);
        }
    }
}
