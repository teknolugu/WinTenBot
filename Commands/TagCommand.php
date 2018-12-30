<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 22/09/2018
 * Time: 15.48
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Grup;
use App\Kata;
use App\Tag;
use App\Waktu;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

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
        $chatid = $message->getChat()->getId();
        $fromid = $message->getFrom()->getId();
        $mssg_id = $message->getMessageId();
        $pecah = explode(' ', $message->getText(true));
        $repMssg = $message->getReplyToMessage();

        $time = $message->getDate();
        $time1 = Waktu::jedaNew($time);

        $isAdmin = Grup::isAdmin($fromid, $chatid);
        $isSudoer = Grup::isSudoer($fromid);
        if ($isAdmin || $isSudoer) {
            if (strlen($pecah[0]) >= 3 && !Kata::cekKandungan($pecah[0], '-')) {
                $datas = [
                    'tag' => $pecah[0],
                    'id_telegram' => $fromid,
                    'id_grup' => $chatid
                ];

                $tipe_data = 'text';
                if ($repMssg != null) {
                    $konten = $repMssg->getText() ?? $repMssg->getCaption();
                    if ($repMssg->getSticker()) {
                        $tipe_data = 'sticker';
                        $id_data = $repMssg->getSticker()->getFileId();
                    } else if ($repMssg->getDocument()) {
                        $tipe_data = 'document';
                        $id_data = $repMssg->getDocument()->getFileId();
                    } else if ($repMssg->getVideo()) {
                        $tipe_data = 'video';
                        $id_data = $repMssg->getVideo()->getFileId();
                    } else if ($repMssg->getVideoNote()) {
                        $tipe_data = 'videonote';
                        $id_data = $repMssg->getVideoNote()->getFileId();
                    } else if ($repMssg->getVoice()) {
                        $tipe_data = 'voice';
                        $id_data = $repMssg->getVoice()->getFileId();
                    } else if ($repMssg->getPhoto()) {
                        $tipe_data = 'photo';
                        $id_data = $repMssg->getPhoto()[0]->getFileId();
                    }

                    $btn_data = trim(str_replace($pecah[0], '', $message->getText(true)));

                } else {
                    $konten = trim(str_replace($pecah[0], '', $message->getText(true)));
                }

                $datas += [
                    'konten' => $konten,
                    'tipe_data' => $tipe_data,
                    'id_data' => $id_data,
                    'btn_data' => $btn_data
                ];


//                $tags = json_encode(Tag::tambahTag($datas), true);
//                $text = '#️⃣ #' . $pecah[0] .
//                    "\n<b>Status : </b>" . $tags['code'] .
//                    "\n<b>Hasil : </b>" . $tags['message'];

//                Request::deleteMessage([
//                    'chat_id' => $chatid,
//                    'message_id' => $mssg_id
//                ]);

                $text = json_encode($datas);

            } else if (Kata::cekKandungan($pecah[0], '-')) {
                $hapus = Tag::hapusTag([
                    'tag' => str_replace('-', '', $pecah[0]),
                    'chat_id' => $chatid
                ]);
                $hapus = json_decode($hapus, true);
                $text = ' ️Delete ' . $pecah[0] .
                    "\n<b>Status : </b>" . $hapus['code'] .
                    "\n<b>Hasil : </b> " . $hapus['message'];
            } else if (strlen($pecah[0]) < 3) {
                $text = '📛 Tag minimal 3 karakter';
            }

            $time2 = Waktu::jedaNew($time);
            $time = "\n\n ⏱ " . $time1 . ' | ⏳ ' . $time2;

            return Request::sendMessage([
                'chat_id' => $chatid,
                'text' => $text . $time,
                'parse_mode' => 'HTML'
            ]);
        }
    }
}
