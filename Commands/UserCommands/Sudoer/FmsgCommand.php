<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use src\Handlers\ChatHandler;
use src\Model\Fbans;
use src\Model\MalFiles;
use src\Model\UrlLists;
use src\Model\Wordlists;
use src\Utils\Format;
use src\Utils\Uri;
use src\Utils\Words;

class FmsgCommand extends UserCommand
{
	protected $name = 'fmsg';
	protected $description = 'Filtering Message';
	protected $usage = '<fmsg>';
	protected $version = '1.0.0';
	
	private $message;
	private $chatHandler;
	
	/**
	 * Execute command
	 *
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function execute()
	{
		$this->message = $this->getMessage();
		$this->chatHandler = new ChatHandler($this->message);
		$repMssg = $this->message->getReplyToMessage();
		
		if ($repMssg != null) {
			$tmessage = $repMssg;
		} else {
			$tmessage = $this->message;
		}
		
		$mediaType = $tmessage->getType();
		
		if ($this->chatHandler->isSudoer()) {
			$r = $this->chatHandler->sendText('Preparing..', -1);
			$this->chatHandler->deleteMessage();
			$pecah = explode(' ', $tmessage->getText(true));
			if ($pecah[0] == 'all') {
				$this->executeWriteCacheAll();
//				$text = '✍ Menulis Url Lists ke Cache..';
//				$this->chatHandler->editText($text);
//				$writeUrl = UrlLists::writeCache();
//
//				$text .= "\n✍ Menulis Word Lists ke Cache..";
//				$this->chatHandler->editText($text);
//				$writeWord = Wordlists::writeCache();
//
//				$text .= "\n✍ Menulis Malfile Lists ke Cache..";
//				$this->chatHandler->editText($text);
//				$writeFile = MalFiles::writeCache();
//
//				$text .= "\n✍ Menulis Fban Lists ke Cache..";
//				$this->chatHandler->editText($text);
//				$writeFban = Fbans::writeCacheFbans();
//
//				$text .= "\n✍ Menulis Admin Fban Lists ke Cache..";
//				$this->chatHandler->editText($text);
//				Fbans::writeCacheAdminFbans();
//
//				$this->chatHandler->editText('✅ Done!' .
//					"\n<b>Write Url: </b> $writeUrl B - " . Format::formatSize($writeUrl) .
//					"\n<b>Write Word: </b> $writeWord B - " . Format::formatSize($writeWord) .
//					"\n<b>Write File: </b> $writeFile B - " . Format::formatSize($writeFile) .
//					"\n<b>Write Fban: </b> $writeFban B - " . Format::formatSize($writeFban)
//				);
//
//				$this->chatHandler->deleteMessage($this->chatHandler->getSendedMessageId(), 2);
//				if($write){
//				}else{
//					$this->chatHandler->editText("✅ Failed! $write");
//				}
			
			} elseif ($mediaType != 'text' && $mediaType != 'command') {
				if ($tmessage->getDocument() != '') {
					$file_id = $tmessage->getDocument()->getFileId();
				} elseif ($tmessage->getPhoto() != '') {
					$file_id = explode('_', $tmessage->getPhoto()[0]->getFileId())[0];
				} elseif ($tmessage->getSticker() != '') {
					$file_id = $tmessage->getSticker()->getFileId();
				} else {
					$file_id = $tmessage->getVideo()->getFileId();
				}
				
				$r = $this->chatHandler->editText("Executing $file_id");
				$this->executeMediaFilter($file_id, $mediaType);
			} elseif ($mediaType == 'sticker') {
				$this->executeStickerFilter();
			} elseif ($pecah[0] != '') {
				$this->chatHandler->sendText('texting');
				$this->chatHandler->sendText($pecah[0]);
				if (Uri::is_url($pecah[0])) {
					$r = $this->chatHandler->sendText('valid url');
					$this->executeUrlFilter($pecah[0], $pecah[1]);
				} else {
					$r = $this->chatHandler->sendText($mediaType);
					$this->executeKataFilter($pecah[0], $pecah[1]);
				}
			} else {
				$this->chatHandler->editText('👓 Meload tada..');
				$lists = MalFiles::getAll();
				
				$this->chatHandler->editText('✍ Menuls ke cache..');
				MalFiles::writeCache();
				
				$list = '';
				ksort($lists);
				$countList = count($lists);
				if ($countList > 0) {
					foreach ($lists as $lis) {
						$list .= '<code>' . $lis['file_id'] . "</code>\n";
					}
				} else {
					$list = 'No <b>Files</b> blocked globally';
				}
				
				$text = federation_name_short . " Filtering Message\n" .
					"📜 <b>Url-Lists</b>: <code>$countList</code>\n" .
					"===============================\n" .
					trim($list);
				
				$this->chatHandler->editText($text,
					-1,
					BUTTON_FILTERING_MESSAGE);
			}
		} else {
			$r = $this->chatHandler->sendText('asd');
		}
		
		return $r;
	}
	
	/**
	 *
	 */
	private function executeWriteCacheAll()
	{
		$text = '✍ Menulis Url Lists ke Cache..';
		$this->chatHandler->editText($text);
		$writeUrl = UrlLists::writeCache();
		
		$text .= "\n✍ Menulis Word Lists ke Cache..";
		$this->chatHandler->editText($text);
		$writeWord = Wordlists::writeCache();
		
		$text .= "\n✍ Menulis Malfile Lists ke Cache..";
		$this->chatHandler->editText($text);
		$writeFile = MalFiles::writeCache();
		
		$text .= "\n✍ Menulis Fban Lists ke Cache..";
		$this->chatHandler->editText($text);
		$writeFban = Fbans::writeCacheFbans();
		
		$text .= "\n✍ Menulis Admin Fban Lists ke Cache..";
		$this->chatHandler->editText($text);
		Fbans::writeCacheAdminFbans();
		
		$this->chatHandler->editText('✅ Done!' .
			"\n<b>Write Url: </b> $writeUrl B - " . Format::formatSize($writeUrl) .
			"\n<b>Write Word: </b> $writeWord B - " . Format::formatSize($writeWord) .
			"\n<b>Write File: </b> $writeFile B - " . Format::formatSize($writeFile) .
			"\n<b>Write Fban: </b> $writeFban B - " . Format::formatSize($writeFban)
		);
		
		$this->chatHandler->deleteMessage($this->chatHandler->getSendedMessageId(), 2);
	}
	
	/**
	 * @param $file_id
	 * @param $media_type
	 * @return mixed
	 */
	private function executeMediaFilter($file_id, $media_type)
	{
		$datas = [
			'file_id'      => $file_id,
			'type_data'    => $media_type,
			'blocked_by'   => $this->chatHandler->getFromId(),
			'blocked_from' => $this->chatHandler->getChatId(),
		];
		$r = $this->chatHandler->editText('🔄 Menyimpan File..' . json_encode($datas, 128));
		$blok = MalFiles::addFile($datas);
		if ($blok) {
			$this->chatHandler->editText('✍ Menulis File ke Cache..');
			MalFiles::writeCache();
			
			$text = '✅ <b>File</b> berhasil di tambahkan';
		} else {
			$text = 'ℹ  <b>File</b> sudah di tambahkan';
		}
		$text .= "\n$file_id";
		$this->chatHandler->editText($text);
		return $this->chatHandler->deleteMessage($this->chatHandler->getSendedMessageId(), 3);
	}
	
	private function executeStickerFilter()
	{
	}
	
	/**
	 * @param        $url
	 * @param string $class
	 * @return mixed
	 */
	private function executeUrlFilter($url, $class = 'blok')
	{
		$datas = [
			'url'     => $url,
			'class'   => strtolower($class),
			'user_id' => $this->chatHandler->getFromId(),
			'chat_id' => $this->chatHandler->getChatId(),
		];
		
		$r = $this->chatHandler->editText('🔄 Menyimpan Url ke  database..', '-1');
		$blok = UrlLists::addUrl($datas);
		if ($blok->rowCount() > 0) {
			$this->chatHandler->editText('✍ Writing Url ke cache..');
			UrlLists::writeCache();
			$text = '✅ <b>Url</b> berhasil di tambahkan';
		} else {
			$text = '⚠ <b>Url</b> sudah di tambahkan';
		}
		$this->chatHandler->editText($text);
		return $this->chatHandler->deleteMessage($this->chatHandler->getSendedMessageId(), 3);
	}
	
	/**
	 * @param $class
	 * @param $word
	 * @return mixed
	 */
	private function executeKataFilter($word, $class = 'blok')
	{
		$datas = [
			'word'        => Words::clearAlphaNum($word),
			'class'       => strtolower($class),
			'id_telegram' => $this->chatHandler->getFromId(),
			'id_group'    => $this->chatHandler->getChatId(),
		];
		
		$r = $this->chatHandler->editText('🔄 Menyimpan Kata ke  database..', '-1');
		$blok = Wordlists::addWords($datas);
		if ($blok->rowCount() > 0) {
			$this->chatHandler->editText('✍ Menulis Kata ke cache..');
			Wordlists::writeCache();
			$text = '✅ Kata berhasil di tambahkan';
		} else {
			$text = '⚠ Kata sudah di tambahkan';
		}
		
		$this->chatHandler->editText($text);
		return $this->chatHandler->deleteMessage($this->chatHandler->getSendedMessageId(), 3);
	}
}
