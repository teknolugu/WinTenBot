<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 07/09/2018
 * Time: 19.02
 */

namespace Longman\TelegramBot\Commands\UserCommands;
;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use src\Handlers\MessageHandlers;
use src\Model\Settings;
use src\Utils\Converters;
use src\Utils\Words;

class StringCommand extends UserCommand
{
    protected $name = 'string';
    protected $description = 'Manipulate message text or caption media';
    protected $usage = '/string';
    protected $version = '1.0.0';

    /**
     * Execute command
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute()
    {
        $mssg = $this->getMessage();
        $raw_message = $mssg->getText(true);
        $replyMssg = $mssg->getReplyToMessage();
        $mHandler = new MessageHandlers($mssg);
        $chat_id = $mssg->getChat()->getId();

//        $mHandler->deleteMessage();

        $mHandler->sendText('Initializing..', '-1');
        if ($replyMssg != "") {
            $raw_text = $replyMssg->getText();
            $replyTarget = $replyMssg->getMessageId();
        } else {
            $raw_text = $raw_message;
        }

        if($raw_text != "") {
            $mHandler->editText('Randomizing case..');
            $text = Words::randomizeCase($raw_text);
        }else{
            $text = "You must reply message or give parameters";
        }

        return $mHandler->editText($text);
    }
}
