<?php
/**
 * Created by PhpStorm.
 * User: azhe403
 * Date: 28/08/18
 * Time: 21:07
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use WinTenDev\Handlers\ChatHandler;
use WinTenDev\Model\Group;
use WinTenDev\Model\MalFiles;

class MalfileCommand extends UserCommand
{
	protected $name = 'file';
	protected $description = 'Add files to blacklist or whitelist';
	protected $usage = '<file>';
	protected $version = '1.0.0';
	
	public function execute()
	{
		$message = $this->getMessage();
		$chatHandler = new ChatHandler($message);
		$chat_id = $message->getChat()->getId();
		$from_id = $message->getFrom()->getId();
		$replyMssg = $message->getReplyToMessage();
		
		$pecah = explode(' ', $message->getText(true));
		if (Group::isSudoer($from_id)) {
			$chatHandler->deleteMessage();
			$r = $chatHandler->sendText('🔄 Mempersiapkan..', '-1');
			if ($replyMssg != '') {
				if ($replyMssg->getDocument() != '') {
					$file_id = $replyMssg->getDocument()->getFileId();
				} elseif ($replyMssg->getPhoto() != '') {
					$file_id = explode('_', $replyMssg->getPhoto()[0]->getFileId())[0];
				} elseif ($replyMssg->getSticker() != '') {
					$file_id = $replyMssg->getSticker()->getFileId();
				} else {
					$file_id = $replyMssg->getVideo()->getFileId();
				}
				
				$datas = [
					'file_id'      => $file_id,
					'type_data'    => $replyMssg->getType(),
					'blocked_by'   => $from_id,
					'blocked_from' => $chat_id,
				];
			}
			
			if (count($datas) == 4) {
				$r = $chatHandler->editText('🔄 Menyimpan..' . json_encode($datas));
				$blok = MalFiles::addFile($datas);
				if ($blok) {
					$chatHandler->editText('✍ Menulis ke Cache..');
					MalFiles::writeCache();
					
					$text = '✅ <b>File</b> berhasil di tambahkan';
				} else {
					$text = 'ℹ  <b>File</b> sudah di tambahkan';
				}
				$chatHandler->editText($text);
				return $chatHandler->deleteMessage($chatHandler->getSendedMessageId(), 3);
			} elseif ($pecah[0] == 'del') {
				$del = MalFiles::deleteFile(['file_id' => $pecah[1]]);
				if ($del->rowCount() > 0) {
					$text = '✅ <b>File</b> berhasil di hapus';
				} else {
					$text = '⚠ <b>File</b> sudah di hapus';
				}
			} elseif ($pecah[0] == 'all') {
				$chatHandler->editText('👓 Meload tada..');
				$lists = MalFiles::getAll();
				
				$chatHandler->editText('✍ Menuls ke cache..');
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
				$text = "📜 <b>Url-Lists</b>: <code>$countList</code>\n" .
					"===============================\n" .
					trim($list);
			} else {
				$text = 'ℹ <b>/file - Delete message if contain undesirable Files</b>' .
					"\n<b>Usage: </b><code>/file [command] (Reply pesan)</code>" .
					"\n<b>Command: </b><code>blok, del</code>";
			}
			$chatHandler->editText($text);
		} else {
			$r = $chatHandler->sendText("⚠ <b>You isn't sudoer and can't use this feature.</b>");
		}
		return $r;
	}
	
}
