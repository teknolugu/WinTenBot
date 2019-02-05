<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 12.11
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use src\Utils\Time;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class KucingCommand extends UserCommand
{
    protected $name = 'kucing';
    protected $description = 'Get random cat picture based on random.cat';
    protected $usage = '<kucing>';
    protected $version = '1.0.0';

    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $mssg_id = $message->getMessageId();

        $time = $message->getDate();
	    $time = Time::jeda($time);

        $json = file_get_contents('http://aws.random.cat/meow');
        $json = json_decode($json, true);

        $text = 'Kucing gan!';

        return Request::sendPhoto([
            'chat_id' => $chat_id,
            'photo' => $json['file'],
            'caption' => $text . $time,
            'reply_to_message_id' => $mssg_id,
            'parse_mode' => 'HTML'
        ]);
    }
}
