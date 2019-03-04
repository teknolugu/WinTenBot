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
	
	/**
	 * @param $chat_id
	 * @param $user_id
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 */
	public static function promote($chat_id, $user_id)
	{
		$promote_data = [
			'chat_id'              => $chat_id,
			'user_id'              => $user_id,
			'can_change_info'      => false,
			'can_post_messages'    => false,
			'can_edit_messages'    => false,
			'can_delete_messages'  => true,
			'can_invite_users'     => true,
			'can_restrict_members' => true,
			'can_pin_messages'     => true,
			'can_promote_members'  => false,
		];
		return Request::promoteChatMember($promote_data);
	}
	
	/**
	 * @param $chat_id
	 * @param $user_id
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 */
	public static function demote($chat_id, $user_id)
	{
		$depromote_data = [
			'chat_id'              => $chat_id,
			'user_id'              => $user_id,
			'can_change_info'      => false,
			'can_post_messages'    => false,
			'can_edit_messages'    => false,
			'can_delete_messages'  => false,
			'can_invite_users'     => false,
			'can_restrict_members' => false,
			'can_pin_messages'     => false,
			'can_promote_members'  => false,
		];
		return Request::promoteChatMember($depromote_data);
	}
}
