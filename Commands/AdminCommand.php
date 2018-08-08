<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 12.11
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Waktu;
use App\Kata;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class AdminCommand extends UserCommand
{
    protected $name = 'admin';
    protected $description = 'A Admin';
    protected $usage = 'admin';
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
        $mssg_id = $message->getMessageId();

        $time = $message->getDate();
        $time = Waktu::jeda($time);

        $data1 = [
            'chat_id' => $chat_id,
        ];

        $respon = Request::getChatAdministrators($data1);
        $respon = json_decode($respon, true);
        $respon = $respon['result'];
        $ngadmins = [];
        if ($respon !== null) {
            $num = 1;
            foreach ($respon as $admin) {
                $fullname = trim($admin['user']['first_name'] . ' ' . $admin['user']['last_name']);
                $fullname = Kata::substrteks($fullname, 30);
                if ($fullname == null) {
                    $fullname = 'Deletted accunnt';
                }
                if ($admin['status'] === 'creator') {
                    $creator = "<a href='tg://user?id=" . $admin['user']['id'] . "'>" . $fullname . '</a>';
                } else {
                    $ngadmins[] = "<a href='tg://user?id=" . $admin['user']['id'] . "'>" . $fullname . '</a>';
                    $num++;
                }
                sort($ngadmins);
            }
        }
        $ngadmin = '';
        $noAdm = 1;
        $lastAdm = end($ngadmins);
        foreach ($ngadmins as $adminl) {
            if ($adminl != $lastAdm) {
                $ngadmin .= '├ ' . $noAdm . ' . ' . $adminl . "\n";
            } else {
                $ngadmin .= '└ ' . $noAdm . ' . ' . $adminl;
            }
            $noAdm++;
        }

        if ($creator != '') {
            $text = "👤 <b>Creator</b>\n└ " . $creator;
        }

        if ($ngadmin != '') {
            $text .= "\n\n👥️ <b>Administrators: " . count($ngadmins) . "</b>" .
                "\n" . $ngadmin;
        }

        $data = [
            'chat_id' => $chat_id,
            'text' => $text . $time,
            'reply_to_message_id' => $mssg_id,
            'parse_mode' => 'HTML'
        ];

        return Request::sendMessage($data);
    }
}
