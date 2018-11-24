<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 28/10/2018
 * Time: 10.07
 */

namespace Longman\TelegramBot\Commands\UserCommands;


use App\Kata;
use App\Tag;
use App\Waktu;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class GettagCommand extends UserCommand
{

    /**
     * Execute command
     *
     * @return void
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
                            'disable_web_page_preview' => true
                        ];

                        $time2 = Waktu::jedaNew($time);
                        $time = "\n\n ⏱ " . $time1 . " | ⏳ " . $time2;

                        $text = '#️⃣<code>#' . $tag[0]['tag'] . '</code>' .
                            "\n" . $tag[0]['konten'];

                        if ($btn_data !== null && $pecah[1] != '-raw') {
                            $btns = [];
                            $abtn_data = explode(',', $btn_data); // teks1|link1.com teks2|link2.com
                            foreach ($abtn_data as $btn) {
                                $abtn = explode('|', trim($btn));
                                $btns[] = [
                                    'text' => $abtn[0],
                                    'url' => $abtn[1]
                                ];
                            }
                            $btns = array_chunk($btns, 2);
                            $data2['reply_markup'] = new InlineKeyboard([
                                'inline_keyboard' => $btns
                            ]);
                        } else {
                            $text .= "\n" . $btn_data;
                        }

                        $text .= $time;

                        if ($tipe_data === 'text') {
                            $data2['text'] = $text;
                            Request::sendMessage($data2);
                        } elseif ($tipe_data === 'document') {
                            $data2 += [
                                $tipe_data => $id_data,
                                'caption' => $text
                            ];
                            Request::sendDocument($data2);
                        }
//                        elseif ($tipe_data === 'video') {
//                            $data2 += [
//                                'video' => $id_data,
//                                'caption' => $text
//                            ];
//                            Request::sendVideo($data2);
//                        } elseif ($tipe_data === 'audio') {
//                            $data2 += [
//                                'audio' => $id_data,
//                                'caption' => $text
//                            ];
//                            Request::sendAudio($data2);
//                        } elseif ($tipe_data === 'photo') {
//                            $data2 += [
//                                'photo' => $id_data,
//                                'caption' => $text
//                            ];
//                            Request::sendPhoto($data2);
//                        } elseif ($tipe_data === 'sticker') {
//                            $data2 += [
//                                'sticker' => $id_data,
//                                'caption' => $text
//                            ];
//                            Request::sendSticker($data2);
//                        }

                    }
                }
            }
        }
    }
}
