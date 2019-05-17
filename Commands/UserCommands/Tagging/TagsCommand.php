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
use src\Handlers\ChatHandler;
use src\Model\Caches;
use src\Model\Settings;
use src\Model\Tags;

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

//		$chatHandler->sendText('🔄 Loading Tags..','-1');
		$chatHandler->deleteMessage();
		
		$tags_data = Caches::read($chat_id, 'tags');
		
		$hit = count($tags_data);
		if ($hit > 0) {
			$text = "#️⃣  <b>$hit Tags</b>\n\n";
			foreach ($tags_data as $data) {
				$arr[] = '<code>#' . $data['tag'] . '</code>';
			}
			sort($arr);
			$tag = implode(' ', $arr);
			$text .= $tag;
		} else {
			$text = 'Tidak ada Tags di hatiqu';
		}
		
		$r = $chatHandler->sendText($text, '-1');
		
		$setting_data = Caches::read($chat_id, 'group_setting');
		$chatHandler->deleteMessage($setting_data['last_tags_message_id']);
		
		$tags_data = Tags::getTags(['id_chat' => $chat_id]);
		Caches::write($chat_id, 'tags', $tags_data);

//		$welcome_data = Settings::getNew(['chat_id' => $chat_id]);
		
		$last_tags_mssg_id = [
			'last_tags_message_id' => $chatHandler->getSendedMessageId(),
			'chat_id'              => $chat_id,
		];
		
		Caches::write($chat_id, 'group_setting', $last_tags_mssg_id);
		
		Settings::saveNew($last_tags_mssg_id, ['chat_id' => $chat_id]);
		
		return $r;
	}
	
}
