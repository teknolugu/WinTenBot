<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 8/23/2018
 * Time: 10:45 AM
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use WinTenDev\Handlers\ChatHandler;
use WinTenDev\Model\Settings;
use WinTenDev\Model\Tags;

class TagsCommand extends UserCommand
{
	protected $name = 'tags';
	protected $description = 'Get cloud tags in current chat';
	protected $usage = '/tags';
	protected $version = '1.0.0';
	
	/**
	 * Execute command
	 *
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$chatHandler = new ChatHandler($message);
		$chat_id = $message->getChat()->getId();
		
		$chatHandler->sendText('🔄 Loading Tags..', '-1');
		$chatHandler->deleteMessage();
		
		$setting_data = Settings::readCache($chat_id);
//		$tags_data = Tags::getTags($chat_id);
		
		$tags_data = Tags::readCache($chat_id);
		if(is_array($tags_data)) {
			$hit = count($tags_data);
		}
		
		$no = 1;
		if ($hit <= 0) {
			$chatHandler->editText('Loading from Cloud..');
			$tags_data = Tags::getTags($chat_id);
			$hit = count($tags_data);
			
			if ($hit <= 0) {
				return $chatHandler->sendText('Tidak ada Tags di hatiqu');
			}
		}
		
		if ($hit > 0) {
			$chatHandler->editText('Completing..');
			$text = "#️⃣  <b>{$hit} Cloud tags</b>\n\n";
			foreach ($tags_data as $data) {
				$arr[] = "<code>#{$data['tag']}</code>\n";
			}
			sort($arr);
			$tag = implode('', $arr);
			$text .= trim($tag);
			$r = $chatHandler->editText($text);
		}
		
		$chatHandler->deleteMessage($setting_data[0]['last_tags_message_id']);
		
		// Write Tags to Cache
		$tags_data = Tags::getTags($chat_id);
		Tags::writeCache($chat_id, $tags_data);
		
		// Save last_tags_message_id
		$last_tags_mssg_id = [
			'last_tags_message_id' => $chatHandler->getSendedMessageId(),
			'chat_id'              => $chat_id,
		];
		
		Settings::saveNew($last_tags_mssg_id, ['chat_id' => $chat_id]);
		
		// Write Settings to Cache
		$setting_data = Settings::getNew(['chat_id' => $chat_id]);
		Settings::writeCache($chat_id, $setting_data);
		
		return $r;
	}
	
}
