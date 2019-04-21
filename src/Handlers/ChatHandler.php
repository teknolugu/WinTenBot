<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 2/14/2019
 * Time: 11:38 PM
 */

namespace src\Handlers;

use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use src\Utils\Time;

class ChatHandler
{
    protected $chat_id;
    protected $from_id;
    protected $date;
    protected $is_reply = false;
    protected $responses;
    protected $reply_to_message_id;
    protected $message_id;
    protected $timeInit;
    protected $timeProc;

    /**
     * ChatHandler constructor.
     *
     * @param $param
     */
    public function __construct(Message $param)
    {
        $this->date = $param->getDate();
        $this->chat_id = $param->getChat()->getId();
        $this->from_id = $param->getFrom()->getId();
        $this->timeInit = "\n\n⏱ " . Time::jedaNew($this->date);
        $this->message_id = $param->getMessageId();
        if ($param->getReplyToMessage() != '') {
            $this->reply_to_message_id = $param->getReplyToMessage()->getMessageId();
        }
    }

    /**
     * @param $text
     * @param null $messageId
     * @param null $keyboard
     * @return ServerResponse
     * @throws TelegramException
     */
    final public function sendText($text, $messageId = null, $keyboard = null)
    {
        $this->timeProc = Time::jedaNew($this->date);
        if ($text != '') {
            $text .=  $this->timeInit . ' | ⌛ ' . $this->timeProc;
        }
        $data = [
            'chat_id' => $this->chat_id,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];

        if ($messageId != '') {
            $data['reply_to_message_id'] = $messageId;
        } else {
            $data['reply_to_message_id'] = $this->message_id;
        }

        if ($keyboard !== null) {
            $data['reply_markup'] = new InlineKeyboard([
                'inline_keyboard' => array_chunk($keyboard, 2),
            ]);
        }
        $this->responses = Request::sendMessage($data);

        return $this->responses;
    }

    /**
     * @param      $text
     * @param null $messageId
     * @param null $keyboard
     * @return ServerResponse
     * @throws TelegramException
     */
    public function editText($text, $messageId = null, $keyboard = null)
    {
        $mssg_id = $this->responses->result->message_id;
        $this->timeProc = Time::jedaNew($this->date);
        if ($text != '') {
            $text .= $this->timeInit . ' | ' . $this->timeProc;
        }
        $data = [
            'chat_id' => $this->chat_id,
            'text' => $text,
            'message_id' => $mssg_id,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];
        if ($messageId != '') {
            $data['reply_to_message_id'] = $messageId;
        } else {
            $data['reply_to_message_id'] = $this->message_id;
        }

        if ($keyboard !== null) {
            $data['reply_markup'] = new InlineKeyboard([
                'inline_keyboard' => array_chunk($keyboard, 2),
            ]);
        }
        return Request::editMessageText($data);
    }

    /**
     * @param $user_id
     * @param bool $unban
     * @return ServerResponse
     */
    public function kickMember($user_id, $unban = false)
    {
        $kick_data = [
            'chat_id' => $this->chat_id,
            'user_id' => $user_id,
        ];

        $r = Request::kickChatMember($kick_data);

        if ($unban) {
            Request::unbanChatMember($kick_data);
        }

        return $r;
    }

    public function deleteMessage($id = null)
    {
        return Request::deleteMessage([
            'chat_id' => $this->chat_id,
            'message_id' => $id ?? $this->message_id,
        ]);
    }
}
