<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 2/14/2019
 * Time: 11:38 PM
 */

namespace src\Handlers;

use Longman\TelegramBot\Request;

class ChatHandler
{
	protected $chat_id;
	protected $from_id;
	protected $date;
	
	/**
	 * ChatHandler constructor.
	 *
	 * @param $param
	 */
	public function __construct($param)
	{
		$param = json_decode($param, true);
		$this->date = $param['date'];
		$this->chat_id = $param['chat']['id'];
		$this->from_id = $param['from']['id'];
	}
	
	/**
	 * @param $user_id
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 */
	public function kickMember($user_id, $unban = false)
	{
		$kick_data = [
			'chat_id' => $this->chat_id,
			'user_id' => $user_id,
		];
		
		if ($unban) {
			Request::unbanChatMember($kick_data);
		}
		
		return Request::kickChatMember($kick_data);
	}
	
	public function unbanMember()
	{
	}
}
