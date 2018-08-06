<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 11.43
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use App\Waktu\Waktu;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class LeftchatmemberCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'leftchatmember';
    /**
     * @var string
     */
    protected $description = 'Left Chat Member';
    /**
     * @var string
     */
    protected $version = '1.0.0';

    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $leftMem = $message->getLeftChatMember();

        $leftMemFname = $leftMem->getFirstName();
        $time = $message->getDate();
        $time = Waktu::jeda($time);

        if (isset($leftMem)) {
            $text = "<b>Dikeluarkan : </b> {$leftMemFname}";
            $data = [
                'chat_id' => $chat_id,
                'text' => $text . $time,
                'parse_mode' => 'HTML'
            ];
//            return Request::sendMessage($data);
        }
    }

}
