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
	protected $message_link;
	
	public $isPrivateChat = false;
	public $isPrivateGroup = false;
	public $callBackQueryId = 0;
	public $callBackMessageId = 0;
	
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
		$this->message_link = str_replace('-100', '', $this->message_id);
		
		if ($param->getReplyToMessage() != '') {
			$this->reply_to_message_id = $param->getReplyToMessage()->getMessageId();
		}
		
		$this->isPrivateChat = $param->getChat()->isPrivateChat();
		if ($param->getChat()->getUsername() == '') {
			$this->isPrivateGroup = true;
		}
	}
	
	/**
	 * @param      $text
	 * @param null $messageId
	 * @param null $keyboard
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	final public function sendText($text, $messageId = null, $keyboard = null)
	{
		$this->timeProc = Time::jedaNew($this->date);
		if ($text != '') {
			$text .= $this->timeInit . ' | ⌛ ' . $this->timeProc;
		}
		$data = [
			'chat_id'                  => $this->chat_id,
			'text'                     => $text,
			'parse_mode'               => 'HTML',
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
	
	final public function sendPrivateText($text, $messageId = null, $keyboard = null)
	{
		$this->timeProc = Time::jedaNew($this->date);
		if ($text != '') {
			$text .= $this->timeInit . ' | ⌛ ' . $this->timeProc;
		}
		$data = [
			'chat_id'                  => $this->from_id,
			'text'                     => $text,
			'parse_mode'               => 'HTML',
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
	 * @param int  $messageId
	 * @param null $keyboard
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	final public function editText($text, $messageId = -1, $keyboard = null)
	{
		$mssg_id = $this->responses->result->message_id;
		$this->timeProc = Time::jedaNew($this->date);
		if ($text != '') {
			$text .= $this->timeInit . ' | ⌛ ' . $this->timeProc;
		}
		$data = [
			'chat_id'                  => $this->chat_id,
			'text'                     => $text,
			'message_id'               => $mssg_id,
			'parse_mode'               => 'HTML',
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
	 * @param      $text
	 * @param int  $messageId
	 * @param null $keyboard
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	final public function editMessageCallback($text, $messageId = -1, $keyboard = null)
	{
		$this->timeProc = Time::jedaNew($this->date);
//	    if ($text != '') {
//		    $text .= $this->timeInit . ' | ⌛ ' . $this->timeProc;
//	    }
		$data = [
			'chat_id'                  => $this->chat_id,
			'text'                     => trim($text),
			'message_id'               => $this->callBackMessageId,
			'parse_mode'               => 'HTML',
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
	 * @param $text
	 * @return ServerResponse
	 */
	final public function answerCallbackQuery($text)
	{
		$data = [
			'callback_query_id' => $this->callBackQueryId,
			'text'              => $text,
			'show_alert'        => true,
			'cache_time'        => 5,
		];
		return Request::answerCallbackQuery($data);
	}
	
	/**
	 * @param      $user_id
	 * @param bool $unban
	 * @return ServerResponse
	 */
	final public function kickMember($user_id, $unban = false)
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
	
	/**
	 * @param int    $user_id
	 * @param string $time
	 * @return ServerResponse
	 */
	final public function restrictMember($user_id, $time = '-1')
	{
		if ($time != '-1') {
			$waktu = explode(':', $time);
			$until = strtotime('+' . $waktu[0] . 'days' . $waktu[1] . 'hours' . $waktu[2] . 'minutes');
		} else {
			$until = strtotime(date('Y-m-d H:i:s'));
		}
		
		$mute = [
			'chat_id'                   => $this->chat_id,
			'user_id'                   => $user_id,
			'until_date'                => $until,
			'can_send_messages'         => false,
			'can_send_media_messages'   => false,
			'can_send_other_messages'   => false,
			'can_add_web_page_previews' => false,
		];
		
		$unmute = [
			'chat_id'                   => $this->chat_id,
			'user_id'                   => $user_id,
			'until_date'                => strtotime(date('Y-m-d H:i:s')),
			'can_send_messages'         => true,
			'can_send_media_messages'   => true,
			'can_send_other_messages'   => true,
			'can_add_web_page_previews' => true,
		];
		$data = $time == -1 ? $unmute : $mute;
		return Request::restrictChatMember($data);
	}
	
	final public function deleteMessage($id = null, $delay = 0)
	{
		sleep($delay);
		return Request::deleteMessage([
			'chat_id'    => $this->chat_id,
			'message_id' => $id ?? $this->message_id,
		]);
	}
	
	final public function leaveChat()
	{
		return Request::leaveChat(['chat_id' => $this->chat_id]);
	}
	
	final public function getSendedMessageId()
	{
		return $this->responses->result->message_id;
	}
	
	final public function getMessageLink()
	{
		if ($this->isPrivateGroup) {
			$message_link = 'https://t.me/c/' . $this->message_link . '/' . $this->message_id;
		} else {
			$message_link = 'https://t.me/' . $this->chat_id . '/' . $this->message_id;
		}
		return $message_link;
	}
}
