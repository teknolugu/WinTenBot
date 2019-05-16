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
use src\Handlers\MessageHandlers;
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
		$mHandler = new MessageHandlers($message);
		$chat_id = $message->getChat()->getId();
		
		$mHandler->sendText('🔄 Loading Tags..');
		$tags_data = Tags::getTags([
			'id_chat' => $chat_id,
		]);
		
		$hit = count($tags_data);
		if ($hit > 0) {
			$text = "#️⃣  <b>$hit Tags</b>\n-------\n";
			foreach ($tags_data as $data) {
				$arr[] = '<code>#' . $data['tag'] . '</code>';
			}
			sort($arr);
			$tag = implode(' ', $arr);
			$text .= $tag;
		} else {
			$text = 'Tidak ada Tags di hatiqu';
		}
		
		$r = $mHandler->editText($text);
		
		$welcome_data = Settings::getNew(['chat_id' => $chat_id]);
		$mHandler->deleteMessage($welcome_data[0]['last_tags_message_id']);
		
		Settings::saveNew([
			'last_tags_message_id' => $r->result->message_id,
			'chat_id'              => $chat_id,
		], [
			'chat_id' => $chat_id,
		]);
		
		return $r;
	}
}
