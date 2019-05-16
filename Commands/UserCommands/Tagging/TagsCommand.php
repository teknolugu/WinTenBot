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
use Longman\TelegramBot\Request;
use src\Handlers\ChatHandler;
use src\Handlers\MessageHandlers;
use src\Model\Settings;
use src\Model\Tags;
use src\Utils\Words;

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
		$rawText = $message->getText(true);
		
		if (Words::cekKandungan($rawText, '#')) {
			return $this->parseTags($rawText);
		}
		
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
	
	/**
	 * @param $rawMessage
	 * @return ServerResponse|void
	 * @throws TelegramException
	 */
	private function parseTags($rawMessage)
	{
		$message = $this->getMessage();
		$chatid = $message->getChat()->getId();
		$mssg_id = $message->getMessageId();
		$chatHandler = new ChatHandler($message);
		$repMssg = $message->getReplyToMessage();
		$pecah = explode(' ', $rawMessage);
		$limit = 1;
		foreach ($pecah as $pecahan) {
			if (($limit <= 3) && Words::cekKandungan($pecahan, '#')) {
				$pecahan = ltrim($pecahan, '#');
				$hashtag = Words::cekKandungan($pecahan, '#');
				if (!$hashtag && strlen($pecahan) >= 3) {
					$tag = Tags::getTags([
						'id_chat' => $chatid,
						'tag'     => $pecahan,
					]);
					
					if ($repMssg != null) {
						$mssg_id = $repMssg->getMessageId();
					}
					
					$id_data = $tag[0]['id_data'];
					$tipe_data = $tag[0]['type_data'];
					$btn_data = $tag[0]['btn_data']; // teks1|link1.com, teks2|link2.com
					
					$text = '#️⃣<code>#' . $tag[0]['tag'] . '</code>' .
						"\n" . $tag[0]['content'];
					
					$btns = [];
					if ($btn_data != null) {
//							if ($pecah[1] != '-r') {
						$abtn_data = explode(',', $btn_data); // teks1|link1.com teks2|link2.com
						foreach ($abtn_data as $btn) {
							$abtn = explode('|', trim($btn));
							$btns[] = [
								'text' => trim($abtn[0]),
								'url'  => trim($abtn[1]),
							];
						}
//
//							} else {
//								$text .= "\n" . $btn_data;
//							}
					}
					
					if ($tipe_data == 'text') {
						$data['text'] = $text;
						$r = $chatHandler->sendText($text, $mssg_id, $btns);
					} else {
						$data = [
							'chat_id'                  => $chatid,
							'parse_mode'               => 'HTML',
							'reply_to_message_id'      => $mssg_id,
							'disable_web_page_preview' => true,
							$tipe_data                 => $id_data,
							'caption'                  => $text,
						];
						
						switch ($tipe_data) {
							case 'document':
								$r = Request::sendDocument($data);
								break;
							case 'video':
								$r = Request::sendVideo($data);
								break;
							case 'voice':
								$r = Request::sendVoice($data);
								break;
							case 'photo':
								$r = Request::sendPhoto($data);
								break;
							case 'sticker':
								$r = Request::sendSticker($data);
								break;
						}
					}
					$limit++;
				}
			}
//			else{
//				$chatHandler->sendText("Due performance reason, we limit 3 batch call tags");
//				break;
//			}
		}
		return $r;
	}
}
