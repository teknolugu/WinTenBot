<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use WinTenDev\Handlers\ChatHandler;
use WinTenDev\Utils\Csv;

class FimportCommand extends UserCommand
{
	
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
		
		if ($chatHandler->isSudoer()) {
			$repMssg = $message->getReplyToMessage();
			if ($repMssg != '') {
				if ($repMssg->getDocument() != '') {
					$chatHandler->sendText('⬇ Importing..');
					$file_id = $repMssg->getDocument()->getFileId();
					$file = Request::getFile(['file_id' => $file_id]);
					Request::downloadFile($file->getResult());
					$file_path = $this->telegram->getDownloadPath() . '/' . $file->getResult()->getFilePath();
					$a = ' file is located at: ' . $file_path;
					
					$chatHandler->editText('🔄 Parsing data..');
					$writeTo = botData . 'temp/fimport.csv';
//					$json = Csv::ReadCsv($file_path, $writeTo);
					$json = Csv::ConvertJson($file_path, ['user_id', 'reason']);
					$datas = \GuzzleHttp\json_decode($json, true);
					if(count($datas[0]) == 2) {
						$chatHandler->editText("Preparing before insert..");
						$forInsert = [];
						$i = 0;
						foreach ($datas as $key => $row) {
							$forInsert[$i]['user_id'] = $row['user_id'];
							$forInsert[$i]['reason_ban'] = $row['reason_ban'];
							$forInsert[$i]['banned_by'] = 'Manual Importer';
							$forInsert[$i]['banned_from'] = $chatHandler->getFromId();
							$i++;
						}
						
						$r = $chatHandler->editText('✅ Done');
						$chatHandler->sendPrivateText($a);
					}else{
						$chatHandler->editText("Invalid");
					}
				}
			} else {
				$r = $chatHandler->sendText('ℹ Repli');
			}
		} else {
			$r = $chatHandler->sendText('ℹ Asd');
		}
		return $r;
	}
}
