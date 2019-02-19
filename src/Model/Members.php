<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 2/19/2019
 * Time: 9:18 PM
 */

namespace src\Model;

use Longman\TelegramBot\Request;

class Members
{
	/**
	 * @param $chat_id
	 * @param $user_id
	 * @param $until
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 */
	public static function muteMember($chat_id, $user_id, $until)
	{
		$mute = [
			'chat_id'                   => $chat_id,
			'user_id'                   => $user_id,
			'until_date'                => 365,
			'can_send_messages'         => false,
			'can_send_media_messages'   => false,
			'can_send_other_messages'   => false,
			'can_add_web_page_previews' => false,
		];
		
		$unmute = [
			'chat_id'                   => $chat_id,
			'user_id'                   => $user_id,
			'until_date'                => strtotime(date('Y-m-d H:i:s')),
			'can_send_messages'         => true,
			'can_send_media_messages'   => true,
			'can_send_other_messages'   => true,
			'can_add_web_page_previews' => true,
		];
		
		$data = $until > 0 ? $mute : $unmute;
		return Request::restrictChatMember($data);
	}
}
