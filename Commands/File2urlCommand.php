<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 06/08/2018
 * Time: 13.23
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Waktu;
use App\FtpUpload;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class File2urlCommand extends UserCommand
{
    protected $name = 'url2file';
    protected $description = 'Konversi URL jadi File';
    protected $usage = '/file2url';
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

        $chat_id = $message->getChat()->getId();
        $mssg_id = $message->getMessageId();
        $text = '';
        $time = $message->getDate();
        $time = Waktu::jeda($time);

        $repMssg = $message->getReplyToMessage();
        $file_id = $repMssg->getDocument()->getFileId();

        if ($repMssg !== null) {
            $text .= "<b>Ini url nya gan..!!</b>\n" . $file_id;
            $respFile = Request::getFile(['file_id' => $file_id]);
            if ($respFile->isOk()) {
                $files = $respFile->getResult();
                $dirUpl = $this->telegram->getDownloadPath();
                Request::downloadFile($files);
                $text .= FtpUpload::aplod($dirUpl . '/documents/file_0.exe');
            } else {
                $text .= 'Error!!';
            }
        } else {
            $text = '🚫 <b>Reply File yang akan di conpert!!</b>';
        }
        $data = [
            'chat_id' => $chat_id,
            'text' => $text . $time,
            'reply_to_message_id' => $mssg_id,
            'parse_mode' => 'HTML'
        ];

        return Request::sendMessage($data);
    }
}
