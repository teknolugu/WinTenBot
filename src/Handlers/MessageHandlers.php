<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 2/6/2019
 * Time: 3:17 PM
 */

namespace src\Handlers;

use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;
use src\Utils\Time;

class MessageHandlers
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
	 * @param $param
	 */
	public function __construct($param)
	{
		$param = json_decode($param, true);
		$this->date = $param['date'];
		$this->timeInit = "\n\n" . Time::jedaNew($this->date);
		$this->chat_id = $param['chat']['id'];
		$this->from_id = $param['from']['id'];
		$this->message_id = $param['message_id'];
		$this->reply_to_message_id = $param['reply_to_message']['message_id'];
	}
	
	/**
	 * @param $text
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	final public function sendText($text, $messageId = null, $keyboard = null)
	{
		$this->timeProc = Time::jedaNew($this->date);
		$data = [
			'chat_id'                  => $this->chat_id,
			'text'                     => $text . $this->timeInit . ' | ' . $this->timeProc,
			'parse_mode'               => 'HTML',
			'disable_web_page_preview' => true,
		];
		
		if ($messageId !== '') {
			$data['reply_to_message_id'] = $messageId;
		} elseif ($messageId === '-1') {
			$data['reply_to_message_id'] = null;
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
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	public function editText($text, $messageId = null, $keyboard = null)
	{
		$mssg_id = $this->responses->result->message_id;
		$this->timeProc = Time::jedaNew($this->date);
		$data = [
			'chat_id'                  => $this->chat_id,
			'text'                     => $text . $this->timeInit . ' | ' . $this->timeProc,
			'message_id'               => $mssg_id,
			'parse_mode'               => 'HTML',
			'disable_web_page_preview' => true,
		];
		if ($messageId !== '') {
			$data['reply_to_message_id'] = $messageId;
		} elseif ($messageId === '-1') {
			$data['reply_to_message_id'] = null;
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
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 */
	public function deleteMessage($id = null)
	{
		return Request::deleteMessage([
			'chat_id'    => $this->chat_id,
			'message_id' => $id ?? $this->message_id,
		]);
	}
	
	public function forwardMessage()
	{
	}
}
