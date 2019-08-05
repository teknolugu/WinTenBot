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
use src\Model\Group;
use src\Model\Logs;
use src\Utils\Time;

class ChatHandler
{
	public $message;
	protected $chatId;
	public $chatTitle;
	protected $from_id;
	protected $date;
	protected $is_reply = false;
	protected $responses;
	protected $reply_to_message_id;
	protected $message_id;
	protected $timeInit;
	protected $timeProc;
	protected $message_link;
	
	public $messageTextWithCmd;
	public $messageText;
	
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
		$this->message = $param;
		$this->date = $param->getDate();
		$this->chatId = $param->getChat()->getId();
		$this->chatTitle = $param->getChat()->getTitle();
		$this->from_id = $param->getFrom()->getId();
		$this->timeInit = "\n\n⏱ " . Time::jedaNew($this->date);
		$this->message_id = $param->getMessageId();
		$this->message_link = str_replace('-100', '', $this->message_id);
		
		$this->messageText = $param->getText(true);
		$this->messageTextWithCmd = $param->getText();
		
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
	final public function sendText($text, $messageId = null, $keyboard = null): ServerResponse
	{
		$this->timeProc = Time::jedaNew($this->date);
		if ($text != '') {
			$text .= $this->timeInit . ' | ⌛ ' . $this->timeProc;
		}
		$data = [
			'chat_id'                  => $this->chatId,
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
	 * @param string $mediaId
	 * @param string $mediaType
	 * @param string $caption
	 * @param int    $messageId
	 * @param array  $keyboard
	 * @return ServerResponse
	 */
	final public function sendMedia(string $mediaId,
	                                string $mediaType,
	                                string $caption = null,
	                                int $messageId = -1,
	                                array $keyboard = null): ServerResponse
	{
		$this->timeProc = Time::jedaNew($this->date);
		
		if ($caption != '') {
			$caption .= $this->timeInit . ' | ⌛ ' . $this->timeProc;
		}
		$data = [
			'chat_id'                  => $this->chatId,
			'caption'                  => $caption,
			$mediaType                 => $mediaId,
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
		
		$res = null;
		switch ($mediaType) {
			case 'document':
				$res = Request::sendDocument($data);
				break;
		}
		
		return $res;
	}
	
	/**
	 * @param      $text
	 * @param null $messageId
	 * @param null $keyboard
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	final public function sendPrivateText($text, $messageId = null, $keyboard = null): ServerResponse
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
	 */
	final public function editText($text, $messageId = -1, $keyboard = null): ServerResponse
	{
		$mssg_id = $this->responses->result->message_id;
		$this->timeProc = Time::jedaNew($this->date);
		if ($text != '') {
			$text .= $this->timeInit . ' | ⌛ ' . $this->timeProc;
		}
		$data = [
			'chat_id'                  => $this->chatId,
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
	 * @param      $media_id
	 * @param null $caption
	 * @param int  $messageId
	 * @param      $keyboard
	 * @return ServerResponse
	 */
	final public function editMedia($media_id, $caption = null, $messageId = -1, $keyboard): ServerResponse
	{
		$mssg_id = $this->getSendedMessageId();
		
		$this->timeProc = Time::jedaNew($this->date);
		
		if ($caption != '') {
			$caption .= $this->timeInit . ' | ⌛ ' . $this->timeProc;
		}
		$data = [
			'chat_id'                  => $this->chatId,
			'text'                     => $caption,
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
		return Request::editMessageMedia($data);
	}
	
	/**
	 * @param      $text
	 * @param int  $messageId
	 * @param null $keyboard
	 * @return ServerResponse
	 */
	final public function editMessageCallback($text, $messageId = -1, $keyboard = null): ServerResponse
	{
		$this->timeProc = Time::jedaNew($this->date);
//	    if ($text != '') {
//		    $text .= $this->timeInit . ' | ⌛ ' . $this->timeProc;
//	    }
		$data = [
			'chat_id'                  => $this->chatId,
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
	final public function answerCallbackQuery($text): ServerResponse
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
	final public function kickMember($user_id, $unban = false): ServerResponse
	{
		$kick_data = [
			'chat_id' => $this->chatId,
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
	final public function restrictMember($user_id, $time = '-1'): ServerResponse
	{
		if ($time != '-1') {
			$waktu = explode(':', $time);
			$until = strtotime('+' . $waktu[0] . 'days' . $waktu[1] . 'hours' . $waktu[2] . 'minutes');
		} else {
			$until = strtotime(date('Y-m-d H:i:s'));
		}
		
		$mute = [
			'chat_id'                   => $this->chatId,
			'user_id'                   => $user_id,
			'until_date'                => $until,
			'can_send_messages'         => false,
			'can_send_media_messages'   => false,
			'can_send_other_messages'   => false,
			'can_add_web_page_previews' => false,
		];
		
		return Request::restrictChatMember($mute);
	}
	
	/**
	 * @param $user_id
	 * @return ServerResponse
	 */
	final public function unrestrictMember($user_id): ServerResponse
	{
		$unmute = [
			'chat_id'                   => $this->chatId,
			'user_id'                   => $user_id,
			'until_date'                => strtotime(date('Y-m-d H:i:s')),
			'can_send_messages'         => true,
			'can_send_media_messages'   => true,
			'can_send_other_messages'   => true,
			'can_add_web_page_previews' => true,
		];
		
		return Request::restrictChatMember($unmute);
	}
	
	/**
	 * @param null $id
	 * @param int  $delay
	 * @return ServerResponse
	 */
	final public function deleteMessage($id = null, $delay = 0): ServerResponse
	{
		sleep($delay);
		return Request::deleteMessage([
			'chat_id'    => $this->chatId,
			'message_id' => $id ?? $this->message_id,
		]);
	}
	
	/**
	 * @param null $chatId
	 * @return ServerResponse
	 */
	final public function leaveChat($chatId = null): ServerResponse
	{
		return Request::leaveChat([
			'chat_id' => $chatId ?? $this->chatId,
		]);
	}
	
	/**
	 * @param      $text
	 * @param null $keyboard
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	final public function logToChannel($text, $keyboard = null): ServerResponse
	{
		$log = "<b>Chat ID: </b>{$this->chatId}" .
			"\n<b>Chat Title: </b>{$this->chatTitle}" .
			"\n<b>From ID:</b> {$this->from_id}" .
			"\n$text";
		return Logs::toChannel($log, $keyboard);
	}
	
	/**
	 * @return int
	 */
	final public function getFromId(): int
	{
		return $this->from_id;
	}
	
	/**
	 * @return int
	 */
	final public function getChatId(): int
	{
		return $this->chatId;
	}
	
	/**
	 * @return string
	 */
	final public function getChatTitle(): string
	{
		return $this->chatTitle;
	}
	
	/**
	 * @return mixed
	 */
	final public function getSendedMessageId(): int
	{
		return $this->responses->result->message_id;
	}
	
	/**
	 * @return string
	 */
	final public function getMessageLink(): string
	{
		if ($this->isPrivateGroup) {
			$message_link = 'https://t.me/c/' . $this->message_link . '/' . $this->message_id;
		} else {
			$message_link = 'https://t.me/' . $this->chatId . '/' . $this->message_id;
		}
		return $message_link;
	}
	
	/**
	 * @param null $user_id
	 * @return bool
	 */
	final public function isAdmin($user_id = null): bool
	{
		$isAdmin = false;
		$res = Request::getChatMember([
			'chat_id' => $this->chatId,
			'user_id' => $user_id ?? $this->from_id,
		]);
		
		if ($res->isOk()) {
			$datas = \GuzzleHttp\json_decode($res, true);
			
			if ($datas['result']['status'] != 'member') {
				$isAdmin = true;
			}
		}
		return $isAdmin;
	}
	
	/**
	 * @param null $from_id
	 * @return bool
	 */
	final public function isSudoer($from_id = null): bool
	{
		$user_id = $from_id ?? $this->from_id;
		return Group::isSudoer($user_id);
	}
}
