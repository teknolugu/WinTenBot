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
use Longman\TelegramBot\Request;
use WinTenDev\Handlers\ChatHandler;

class PollCommand extends UserCommand
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

        if(!$mssg->getChat()->isPrivateChat()) {
            if ($replyMssg != "") {
                $raw_message = $replyMssg->getText();
                $replyTarget = $replyMssg->getMessageId();
            }

            if ($raw_message != "") { //asd#a|b|c
                $question = explode("#", $raw_message);
                $raw_polls = explode("|", $question[1]);
                $this->validatePoll();
//            $keyboard = ['text' => 'asdasd', 'link' => 'link.com'];
                $r = Request::sendPoll([
                    'chat_id' => $chat_id,
                    'question' => $question[0],
                    'options' => $raw_polls
                ]);

                return $r;
//            $mHandler->sendText("<b>Result</b>\n" . $r);
            } else {
                $text = "You must reply message or give parameters" .
                    "\nExample: <code>/poll Question?#choice1|choice2|choice3</code>" .
                    "\n\n<b>Note:</b> choice minum 2 characters";
            }
        }else{
            $text = "ℹ Create <b>Poll</b> isn't for <b>Private Chat.</b>";
        }

        return $mHandler->sendText($text);
    }

    /**
     * @return bool
     * @method function validatePoll Asdasd
     */
    private function validatePoll(){
        return true;
    }
}
