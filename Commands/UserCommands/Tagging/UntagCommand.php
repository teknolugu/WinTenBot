<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 22/09/2018
 * Time: 15.48
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use src\Handlers\ChatHandler;
use src\Handlers\MessageHandlers;
use src\Model\Group;
use src\Model\Tags;
use src\Utils\Entities;
use src\Utils\Words;

class UntagCommand extends UserCommand
{
	protected $name = 'untag';
	protected $description = 'Save tag into cloud';
	protected $usage = '/untag <tagnya>';
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
		$chatid = $message->getChat()->getId();
		$fromid = $message->getFrom()->getId();
		$pecah = explode(' ', $message->getText(true));
		
		$isAdmin = Group::isAdmin($fromid, $chatid);
		$isSudoer = Group::isSudoer($fromid);
		$r = $chatHandler->sendText('Memeriksa izin..');
		if ($isAdmin || $isSudoer) {
			$chatHandler->editText('Mempersiapkan..');
			if ($pecah[0] != '' && strlen($pecah[0]) >= 3) {
				$tag = str_replace('-', '', $pecah[0]);
				$chatHandler->editText('Deleting #' . $tag . '..');
				$del = Tags::delTags([
					'tag'     => $tag,
					'id_chat' => $chatid,
				]);
				
				if ($del->rowCount() > 0) {
					$text = 'Hapus tag #' . $tag . ' berhasil';
				} else {
					$text = 'Tag #' . $tag . ' sudah di hapus';
				}
				return $chatHandler->editText($text);
			}
			$text = 'ℹ  Delete tag dari Cloud Tags' .
				"\n<b>Example:\n</b><code>/untag tag - InMessage</code>" .
				"\nLength <code>tag</code> minimum 3 characters.";
			
			$r = $chatHandler->editText($text);
		}
		return $r;
	}
}
