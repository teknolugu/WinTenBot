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
use WinTenDev\Utils\Buttons;
use WinTenDev\Utils\Words;

class WelcomeCommand extends UserCommand
{
	protected $name = 'welcome';
	protected $description = 'Set welcome message, buttons, others';
	protected $usage = '/welcome';
	protected $version = '1.0.0';
	
	protected $chatHandler;
	protected $pecah;
	
	/**
	 * Execute command
	 *
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$this->chatHandler = new ChatHandler($message);
		
		$chat_id = $message->getChat()->getId();
		$from_id = $message->getFrom()->getId();
		
		$isAdmin = Group::isAdmin($from_id, $chat_id);
		$isSudoer = Group::isSudoer($from_id);
		
		if ($isAdmin || $isSudoer) {
			if (!$message->getChat()->isPrivateChat()) {
				$pecah = explode(' ', $message->getText(true));
				$this->pecah = $pecah;
				$this->chatHandler->sendText('Loading data..');
				$commands = ['message', 'button'];
				if (Words::isSameWith($pecah[0], $commands)) {
					$r = $this->saveWelcome();
				} elseif ($pecah[0] == 'media') {
					$r = $this->saveWelcomeMedia();
				} elseif ($pecah[0] == '') {
					$r = $this->getCurrentWelcome();
				} else {
					$r = $this->sendHelp();
				}
			} else {
				$r = $this->chatHandler->sendText('Perintah /welcome hanya di dalam grup');
			}
		}
		
		return $r;
	}
	
	/**
	 *
	 */
	private function updateCache()
	{
		$this->chatHandler->editText("✍ Writing to cache..");
		$setting_data = Settings::getNew(['chat_id' => $this->chatHandler->getChatId()]);
		Settings::writeCache($this->chatHandler->getChatId(), $setting_data);
	}
	
	/**
	 * @return mixed
	 */
	private function saveWelcome(): array
	{
		if ($this->pecah[1] == "reset") {
			$welcome_data = "";
		} else {
			$welcome_data = trim(str_replace($this->pecah[0], '', $this->chatHandler->messageText));
		}
		
		$this->chatHandler->editText('Saving settings..');
		Settings::saveNew([
			'welcome_' . $this->pecah[0] => $welcome_data,
			'chat_id'                    => $this->chatHandler->getChatId(),
		], [
			'chat_id' => $this->chatHandler->getChatId(),
		]);
		
		$this->updateCache();
		return $this->chatHandler->editText('✅ Welcome ' . $this->pecah[0] . ' saved (y)');
	}
	
	/**
	 *
	 */
	private function saveWelcomeMedia(): array
	{
		$repMssg = $this->chatHandler->message->getReplyToMessage();
		if ($repMssg != "") {
			$mediaType = $repMssg->getType();
			$allowedMedia = ['video', 'photo', 'document'];
			if (Words::isSameWith($mediaType, $allowedMedia)) {
				$this->chatHandler->editText("Saving welcome Media $mediaType");
				
				if ($repMssg->getDocument()) {
					$id_data = $repMssg->getDocument()->getFileId();
				} elseif ($repMssg->getVideo()) {
					$id_data = $repMssg->getVideo()->getFileId();
				} else {
					$id_data = $repMssg->getPhoto()[0]->getFileId();
				}
				
				$welcome = [
					'welcome_media'      => $id_data,
					'welcome_media_type' => $mediaType,
					'chat_id'            => $this->chatHandler->getChatId(),
				];
				
				$save = Settings::save($welcome);
				
				if ($save->rowCount() > 0) {
					$asd = "Welcome Media berhasil di simpan";
				} else {
					$asd = "Welcome Media telah di simpan";
				}
				
				$r = $this->chatHandler->editText($asd);
			} else {
				$this->chatHandler->editText("Saat ini belum di dukung untuk tipe ini");
			}
		} else {
			$r = $this->chatHandler->editText("Balas pesan untuk menyimpan sebagai Welcome Media");
		}
		return $r;
	}
	
	/**
	 * @return mixed
	 */
	private function getCurrentWelcome(): array
	{
		$datas = Settings::getNew(['chat_id' => $this->chatHandler->getChatId()]);
		$welcome_media_type = $datas[0]['welcome_media_type'];
		$welcome_media = $datas[0]['welcome_media'];
		$text = "<b>Media type:</b> ";
		
		if ($welcome_media_type != "") {
			$text .= $welcome_media_type;
		} else {
			$text .= "-no welcome media-";
		}
		
		if ($datas[0]['welcome_message'] != '') {
			$text .= "\n<b>Welcome Message</b>" .
				"\n<code>" . $datas[0]['welcome_message'] . '</code>';
		} else {
			$text .= "\nTidak ada konfigurasi pesan welcome, pesan default akan di terapkan";
		}
		
		$btn_markup = [];
		if ($datas[0]['welcome_button'] != '') {
			$btn_data = $datas[0]['welcome_button'];
			if ($this->pecah[0] != '-r') {
				$btn_markup = Buttons::Generate($btn_data);
			} else {
				$text .= "\n\n<b>Button markup</b>\n" . $btn_data;
			}
		}
		
		$this->updateCache();
		
		if ($welcome_media != "") {
			$this->chatHandler->deleteMessage($this->chatHandler->getSendedMessageId());
			$res = $this->chatHandler->sendMedia($welcome_media, $welcome_media_type, $text, "-1", $btn_markup);
		} else {
			$res = $this->chatHandler->editText($text, null, $btn_markup);
		}
		
		return $res;
	}
	
	/**
	 * @return mixed
	 */
	private function sendHelp(): array
	{
		$btn_markup = [
			['text' => 'Contoh Message', 'callback_data' => 'inbot-example_welcome-message-example'],
			['text' => 'Contoh Button', 'callback_data' => 'inbot-example_welcome-button-example'],
		];
		
		return $this->chatHandler->editText('ℹ Parameter tidak valid.' .
			"\nContoh:\n/welcome message pesan" .
			"\n/welcome button text_tombol|link.com", '-1', $btn_markup);
	}
}
