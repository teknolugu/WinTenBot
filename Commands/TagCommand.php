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
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class TagCommand extends UserCommand
{
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
        $pecah = explode(' ', $message->getText());
        $repMssg = $message->getReplyToMessage();

        $time = $message->getDate();
        $time1 = Waktu::jedaNew($time);

        $isAdmin = Grup::isAdmin($fromid, $chatid);
        $isSudoer = Grup::isSudoer($fromid);
        if ($isAdmin || $isSudoer) {
            if ($pecah[0] === '/tag') {
                if (strlen($pecah[1]) >= 3 && !Kata::cekKandungan($pecah[1], '-')) {
                    $datas = [
                        'tag' => $pecah[1],
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

                        $btn_data = trim(str_replace(['/tag', $pecah[1]], '', $message->getText()));

                    } else {
                        $konten = trim(str_replace(['/tag', $pecah[1]], '', $message->getText()));
                    }

                    $datas += [
                        'konten' => $konten,
                        'tipe_data' => $tipe_data,
                        'id_data' => $id_data,
                        'btn_data' => $btn_data
                    ];


                    $tags = json_decode(Tag::tambahTag($datas), true);
                    $text = '#️⃣ #' . $pecah[1] .
                        "\n<b>Status : </b>" . $tags['code'] .
                        "\n<b>Hasil : </b>" . $tags['message'];

                    Request::deleteMessage([
                        'chat_id' => $chatid,
                        'message_id' => $mssg_id
                    ]);

                } else if (Kata::cekKandungan($pecah[1], '-')) {
                    $hapus = Tag::hapusTag([
                        'tag' => str_replace('-', '', $pecah[1]),
                        'chat_id' => $chatid
                    ]);
                    $hapus = json_decode($hapus, true);
                    $text = ' ️' . $pecah[1] .
                        "\n<b>Status : </b>" . $hapus['code'] .
                        "\n<b>Hasil : </b> " . $hapus['message'];
                } else if (strlen($pecah[1]) < 3) {
                    $text = '📛 Tag minimal 3 karakter';
                }

                $time2 = Waktu::jedaNew($time);
                $time = "\n\n ⏱ " . $time1 . ' | ⏳ ' . $time2;

                if ($text !== '') {
                    return Request::sendMessage([
                        'chat_id' => $chatid,
                        'text' => $text . $time,
                        'parse_mode' => 'HTML'
                    ]);
                }
            }
        }

        if (Kata::cekKandungan($message->getText(), '#')) {
            foreach ($pecah as $pecahan) {
                if (Kata::cekKandungan($pecahan, '#')) {
                    $pecahan = ltrim($pecahan, '#');
                    $hashtag = Kata::cekKandungan($pecahan, '#');
                    if (!$hashtag && strlen($pecahan) >= 3) {
                        $tag = Tag::ambilTag([
                            'chat_id' => $chatid,
                            'tag' => $pecahan
                        ]);
                        $tag = json_decode($tag, true)['message'];
                        $text = '#️⃣<code>#' . $tag[0]['tag'] . '</code>' .
                            "\n" . $tag[0]['konten'] .
                            "\n\n" . $time1;
                        if ($repMssg != null) {
                            $mssg_id = $repMssg->getMessageId();
                        }

                        $id_data = $tag[0]['id_data'];
                        $tipe_data = $tag[0]['tipe_data'];
                        $btn_data = $tag[0]['btn_data']; // teks1|link1.com, teks2|link2.com
                        $data2 = [
                            'chat_id' => $chatid,
                            'parse_mode' => 'HTML',
                            'reply_to_message_id' => $mssg_id,
                            'disable_web_preview' => true
                        ];

                        if ($btn_data !== null) {
                            $btn2 = [];
                            $abtn_data = explode(',', $btn_data); // teks1|link1.com teks2|link2.com
                            foreach ($abtn_data as $btn) {
                                $abtn = explode('|', trim($btn));
                                $btn2[] = [
                                    'text' => $abtn[0],
                                    'url' => $abtn[1]
                                ];
                            }
                            $keyboard = new InlineKeyboard($btn2);
                            $data2['reply_markup'] = $keyboard;
                        }

                        if ($tipe_data === 'text') {
                            $data2['text'] = $text;
                            Request::sendMessage($data2);
                        } elseif ($tipe_data === 'document') {
                            $data2 += [
                                'document' => $id_data,
                                'caption' => $text
                            ];
                            Request::sendDocument($data2);
                        } elseif ($tipe_data === 'video') {
                            $data2 += [
                                'video' => $id_data,
                                'caption' => $text
                            ];
                            Request::sendVideo($data2);
                        } elseif ($tipe_data === 'audio') {
                            $data2 += [
                                'audio' => $id_data,
                                'caption' => $text
                            ];
                            Request::sendAudio($data2);
                        } elseif ($tipe_data === 'photo') {
                            $data2 += [
                                'photo' => $id_data,
                                'caption' => $text
                            ];
                            Request::sendPhoto($data2);
                        } elseif ($tipe_data === 'sticker') {
                            $data2 += [
                                'sticker' => $id_data,
                                'caption' => $text
                            ];
                            Request::sendSticker($data2);
                        }
                    }
                }
            }
        }
    }
}
