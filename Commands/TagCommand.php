<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 22/09/2018
 * Time: 15.48
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use src\Handlers\MessageHandlers;
use src\Model\Group;
use src\Model\Tags;
use src\Utils\Words;

class TagCommand extends UserCommand
{
	protected $name = 'tag';
	protected $description = 'Save tag into cloud';
	protected $usage = '/tag <tagnya>';
	protected $version = '1.0.0';
	
	/**
	 * Execute command
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$mHandler = new MessageHandlers($message);
		$chatid = $message->getChat()->getId();
		$fromid = $message->getFrom()->getId();
		$pecah = explode(' ', $message->getText(true));
		$repMssg = $message->getReplyToMessage();
		
		$isAdmin = Group::isAdmin($fromid, $chatid);
		$isSudoer = Group::isSudoer($fromid);
		if ($isAdmin || $isSudoer) {
			$mHandler->sendText('Initializing..');
			if (strlen($pecah[0]) >= 3 && !Words::cekKandungan($pecah[0], '-')) {
				$datas = [
					'tag'     => $pecah[0],
					'id_user' => $fromid,
					'id_chat' => $chatid,
				];
				
				$tipe_data = 'text';
				if ($repMssg !== null) {
					$konten = $repMssg->getText() ?? $repMssg->getCaption();
					if ($repMssg->getSticker()) {
						$tipe_data = 'sticker';
						$id_data = $repMssg->getSticker()->getFileId();
					} elseif ($repMssg->getDocument()) {
						$tipe_data = 'document';
						$id_data = $repMssg->getDocument()->getFileId();
					} elseif ($repMssg->getVideo()) {
						$tipe_data = 'video';
						$id_data = $repMssg->getVideo()->getFileId();
					} elseif ($repMssg->getVideoNote()) {
						$tipe_data = 'videonote';
						$id_data = $repMssg->getVideoNote()->getFileId();
					} elseif ($repMssg->getVoice()) {
						$tipe_data = 'voice';
						$id_data = $repMssg->getVoice()->getFileId();
					} elseif ($repMssg->getPhoto()) {
						$tipe_data = 'photo';
						$id_data = $repMssg->getPhoto()[0]->getFileId();
					}
					
					$btn_data = trim(str_replace($pecah[0], '', $message->getText(true)));
				} else {
					$konten = trim(str_replace($pecah[0], '', $message->getText(true)));
				}
				
				$datas += [
					'content'   => $konten ?? '',
					'type_data' => $tipe_data,
					'id_data'   => $id_data ?? '',
					'btn_data'  => $btn_data ?? '',
				];
				
				$mHandler->editText('Adding ' . $pecah[0] . '..');
				$add = Tags::saveNew($datas, ['tag' => $pecah[0]]);
				if ($add->rowCount() > 0) {
					$text = 'Menambahkan tag #' . $pecah[0] . ' berhasil';
				} else {
					$text = 'Menambahkan tag gagal atau tag yang sudah ada tidak dapat di perbarui.';
				}
			} elseif (Words::cekKandungan($pecah[0], '-')) {
				$tag = str_replace('-', '', $pecah[0]);
				$mHandler->editText('Deleting #' . $tag . '..');
				$del = Tags::delTags([
					'tag'     => $tag,
					'id_chat' => $chatid,
				]);
				
				if ($del->rowCount() > 0) {
					$text = 'Hapus tag #' . $tag . ' berhasil';
				} else {
					$text = 'Pastikan tag ada yang akan di hapus';
				}
			} elseif (strlen($pecah[0]) < 3) {
				$text = 'ℹ  Reply message for save into Cloud Tags' .
					"\n<b>Example:\n</b><code>/tag tag [button|link.button]</code> - InReply" .
					"\n<code>/tag tag content</code> - InMessage" .
					"\nLength <code>tag</code> minimum 3 characters.\nMark [ ] is optional";
			}
			
			$mHandler->deleteMessage();
			$r = $mHandler->editText($text);
		}
		return $r;
	}
}
