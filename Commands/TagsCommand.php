<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 8/23/2018
 * Time: 10:45 AM
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use src\Handlers\MessageHandlers;
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
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$mHandler = new MessageHandlers($message);
		$chat_id = $message->getChat()->getId();
		
		$mHandler->sendText('Loading Tags..');
		$tags_data = Tags::getTags([
			'id_chat' => $chat_id,
		]);
		
		$hit = count($tags_data);
		if ($hit > 0) {
			$text = "#️⃣  <b>$hit Tags</b>\n-------\n";
			foreach ($tags_data as $data) {
				$text .= '<code>#' . $data['tag'] . '</code> ';
			}
		} else {
			$text = 'Tidak ada tags di hatiqu';
		}
		
		return $mHandler->editText($text);
	}
}
