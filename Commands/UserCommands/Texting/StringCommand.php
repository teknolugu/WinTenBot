<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 07/09/2018
 * Time: 19.02
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use WinTenDev\Handlers\ChatHandler;
use WinTenDev\Utils\Words;

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
        $mHandler = new ChatHandler($mssg);
        $chat_id = $mssg->getChat()->getId();
        $bacot = explode(' ', $raw_message);

//        $mHandler->deleteMessage();

        $mHandler->sendText('Initializing..', '-1');
        if ($replyMssg != "") {
            $raw_text = $replyMssg->getText() ?? $replyMssg->getCaption();
            $replyTarget = $replyMssg->getMessageId();
        } else {
            $raw_text = $raw_message;
        }
        $btn_markup = [];
        switch ($bacot[0]){
            case 'stem':
                $mHandler->editText('Stemming..');
                $text = Words::stemText($raw_text); //
                $mHandler->editText('WTF..');
                break;

            case 'rangkum':
                $mHandler->editText("Sedang merankum..");
                $text = \GuzzleHttp\json_encode(Words::rangkumText($raw_text), 128);
                break;

            default:
                $mHandler->editText('Randomizing case..');
                $text = Words::randomizeCase($raw_text);
                break;
        }

        if (!$mssg->getChat()->isPrivateChat()) {
            $urlChatId = str_replace('-100', '', $chat_id);
            $urlBtn = "https://t.me/c/$urlChatId/$replyTarget";
            $btn_markup[] = ['text' => '🔍 Original message', 'url' => $urlBtn];
        }

//        if ($raw_text != "") {
//
//        } else {
//            $text = "You must reply message or give parameters";
//        }

        return $mHandler->editText($text, '', $btn_markup);
    }
}
