<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 07/09/2018
 * Time: 19.02
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use WinTenDev\Handlers\ChatHandler;
use WinTenDev\Model\Group;
use WinTenDev\Model\Settings;

class RulesCommand extends UserCommand
{
	protected $name = 'rules';
	protected $description = 'Get and Set ruleset message';
	protected $usage = '/rules';
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
		$mHandler = new ChatHandler($message);
		$chat_id = $message->getChat()->getId();
		$chat_title = $message->getChat()->getTitle();
//		$mssg_id = $message->getMessageId();
		$from_id = $message->getFrom()->getId();
		$repMssg = $message->getReplyToMessage();
		$pecah = explode(' ', $message->getText(true));
		
		if ($message->getChat()->getType() != 'private') {
			if ($repMssg != '' && $pecah[0] == '-s') {
				$isAdmin = Group::isAdmin($from_id, $chat_id);
				$isSudoer = Group::isSudoer($from_id);
				if ($isAdmin || $isSudoer) {
					$mHandler->sendText('Saving rules..');
					$text = Settings::saveNew([
						'rules_text' => $repMssg->getText(),
						'chat_id'    => $chat_id,
					], ['chat_id' => $chat_id]);
					$r = $mHandler->editText('✅ Rules saved.');
				}
			} else {
				$text = 'Rules group of <b>' . $chat_title . '</b>';
				$r = $mHandler->sendText($text, null, [
					['text' => '📃 Read rules..', 'url' => urlStart . 'rules_' . $chat_id],
				]);
			}
		}
		
		return $r;
	}
}
