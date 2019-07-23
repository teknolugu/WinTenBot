<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use src\Handlers\ChatHandler;
use src\Model\MalFiles;
use src\Model\Tags;
use src\Model\UrlLists;
use src\Model\Wordlists;
use src\Utils\Words;

/**
 * Edited message command
 *
 * Gets executed when a user message is edited.
 */
class EditedmessageCommand extends SystemCommand
{
	protected $name = 'editedmessage';
	protected $description = 'User edited message';
	protected $version = '1.1.1';
	
	/**
	 * Command execute method
	 *
	 * @return bool|ServerResponse
	 * @throws TelegramException
	 */
	public function execute()
	{
		$message = $this->getEditedMessage();
		$chatHandler = new ChatHandler($message);
		$textMssg = $message->getText();
//		$pesanCmd = explode(' ', strtolower($textMssg))[0];
		
		
		// Scan BadMessage
		$isBad = $this->checkMessage();
		if ($isBad) {
			return $isBad;
		}
		
		switch (true) {
			case Words::isContain($textMssg, '#'):
				return $this->parseTags();
				break;
		}

//		if ($message != "") {
//			$res = $chatHandler->sendText('TerEdit?');
//		}

//		return $res;
	}
	
	private function parseTags()
	{
		$message = $this->getMessage();
		$chatid = $message->getChat()->getId();
		$mssg_id = $message->getMessageId();
		$chatHandler = new ChatHandler($message);
		$repMssg = $message->getReplyToMessage();
		$pecah = Words::multiexplode([' ', "\n"], $message->getText());
		$limit = 1;
		foreach ($pecah as $pecahan) {
			if (($limit <= 3) && Words::isContain($pecahan, '#')) {
				$pecahan = ltrim($pecahan, '#');
				$hashtag = Words::isContain($pecahan, '#');
				if (!$hashtag && strlen($pecahan) >= 3) {
					$tag = Tags::getTag([
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
	
	/**
	 * @return bool
	 */
	private function checkMessage()
	{
		$isBad = false;
		$message = $this->getEditedMessage();
		$chatHandler = new ChatHandler($message);
		$textMessage = $message->getCaption() ?? $message->getText();
		$wordScan = Words::clearAlphaNum($textMessage);
		
		if (UrlLists::isContainBadUrl($textMessage)
			|| Wordlists::isContainBadword(strtolower($wordScan))) {
			$chatHandler->deleteMessage();
			$isBad = true;
		}
		
		if ($message->getDocument() != '') {
			$file_id = $message->getDocument()->getFileId();
		} elseif ($message->getPhoto() != '') {
			$file_id = explode('_', $message->getPhoto()[0]->getFileId())[0];
		} elseif ($message->getSticker() != '') {
			$file_id = $message->getSticker()->getFileId();
		} elseif ($message->getVideo() != '') {
			$file_id = $message->getVideo()->getFileId();
		}
		
		if (MalFiles::isMalFile($file_id)) {
			$chatHandler->deleteMessage();
			$isBad = true;
		}
		
		return $isBad;
	}
}
