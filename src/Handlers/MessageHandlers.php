<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 2/6/2019
 * Time: 3:17 PM
 */

namespace src\Handlers;

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
	final public function sendText($text, $reply = false)
	{
		$this->timeProc = Time::jedaNew($this->date);
		$this->responses = Request::sendMessage([
			'chat_id'             => $this->chat_id,
			'text'                => $text . $this->timeInit . ' | ' . $this->timeProc,
			'parse_mode'          => 'HTML',
			'reply_to_message_id' => $reply ? $this->reply_to_message_id : null,
		]);
		
		return $this->responses;
	}
	
	public function editText($text)
	{
		$mssg_id = $this->responses->result->message_id;
		$this->timeProc = Time::jedaNew($this->date);
		return Request::editMessageText([
			'chat_id'    => $this->chat_id,
			'text'       => $text . $this->timeInit . ' | ' . $this->timeProc,
			'message_id' => $mssg_id,
			'parse_mode' => 'HTML',
		]);
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
}
