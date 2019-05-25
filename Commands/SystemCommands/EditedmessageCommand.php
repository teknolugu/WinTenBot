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
use src\Handlers\ChatHandler;
use src\Model\MalFiles;
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
		
		// Scan BadMessage
		$isBad = $this->checkMessage();
		if ($isBad) {
			return $isBad;
		}

//		if ($message != "") {
//			$res = $chatHandler->sendText('TerEdit?');
//		}
		
		return $res;
	}
	
	/**
	 * @return bool
	 */
	private function checkMessage()
	{
		$isBad = false;
		$message = $this->getEditedMessage();
		$chatHandler = new ChatHandler($message);
		
		$wordScan = Words::clearAlphaNum($message->getText());
		if (UrlLists::isContainBadUrl($message->getText())
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
		} else {
			$file_id = $message->getVideo()->getFileId();
		}
		
		if (MalFiles::isMalFile($file_id)) {
			$chatHandler->deleteMessage();
			$isBad = true;
		}
		
		return $isBad;
	}
}
