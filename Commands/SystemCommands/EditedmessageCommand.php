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
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function execute()
	{
		$message = $this->getEditedMessage();
		$chatHandler = new ChatHandler($message);
		
		$forScan = $message->getText() ?? $message->getCaption();
		$isBad = $this->checkMessage($forScan);
		
		if (!$isBad) {
			if ($message != "") {
				$res = $chatHandler->sendText('TerEdit?');
			}
		}
		
		return $res;
	}
	
	/**
	 * @param $messageText
	 * @return bool
	 */
	private function checkMessage($messageText)
	{
		$isBad = false;
		$message = $this->getEditedMessage();
		$chatHandler = new ChatHandler($message);
		
		$wordScan = Words::clearAlphaNum($messageText);
		if (UrlLists::isContainBadUrl($messageText)
			|| Wordlists::isContainBadword(strtolower($wordScan))) {
			$chatHandler->deleteMessage();
			$isBad = true;
		}
		
		return $isBad;
	}
}
