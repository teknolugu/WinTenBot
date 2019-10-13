<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 28/10/2018
 * Time: 14.19
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use WinTenDev\Handlers\ChatHandler;
use WinTenDev\Model\Translator;

class TrCommand extends UserCommand
{
	protected $name = 'tr';
	protected $description = 'Translate text or media caption into your language';
	protected $usage = '<ping>';
	protected $version = '1.0.0';
	/**
	 * @inheritdoc
	 */
	/**
	 * Execute command
	 *
	 * @return ServerResponse
	 * @throws TelegramException
	 * @throws \Exception
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$mHandler = new ChatHandler($message);
		
		$pecah = explode(' ', $message->getText(true));
		$repMssg = $message->getReplyToMessage();
		
		if ($repMssg != '') {
			$tr_data = Translator::Exe($repMssg->getText(), $pecah[0], $pecah[1]);
			$text = '<b>Translate from</b> <code>' . $tr_data['from'] . '</code> <b>to</b> <code>' . $tr_data['to'] . "</code>\n";
			$text .= '<code>' . $tr_data['text'] . '</code>';
		} else {
			$text = 'Reply yang mau di terjemahkan';
		}
		
		return $mHandler->sendText($text);
	}
}
