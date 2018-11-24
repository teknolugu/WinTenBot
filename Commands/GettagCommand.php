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

                        $data = [
                            'chat_id' => $chatid,
                            'parse_mode' => 'HTML',
                            'reply_to_message_id' => $mssg_id,
                            'disable_web_page_preview' => true
                        ];

                        $time2 = Waktu::jedaNew($time);
                        $time = "\n\n ⏱ " . $time1 . " | ⏳ " . $time2;

                        $text = '#️⃣<code>#' . $tag[0]['tag'] . '</code>' .
                            "\n" . $tag[0]['konten'];

                        if ($btn_data !== null) {
                            if ($pecah[1] != '-raw') {
                                $btns = [];
                                $abtn_data = explode(',', $btn_data); // teks1|link1.com teks2|link2.com
                                foreach ($abtn_data as $btn) {
                                    $abtn = explode('|', trim($btn));
                                    $btns[] = [
                                        'text' => $abtn[0],
                                        'url' => $abtn[1]
                                    ];
                                }
                                sort($btns);
                                $btns = array_chunk($btns, 3);
                                $data['reply_markup'] = new InlineKeyboard([
                                    'inline_keyboard' => $btns
                                ]);
                            } else {
                                $text .= "\n" . $btn_data;
                            }
                        }

                        $text .= $time;

                        if ($tipe_data == 'text') {
                            $data['text'] = $text;
                            Request::sendMessage($data);
                        } else {
                            $data += [
                                $tipe_data => $id_data,
                                'caption' => $text
                            ];

                            switch ($tipe_data) {
                                case 'document':
                                    Request::sendDocument($data);
                                    break;
                                case 'video':
                                    Request::sendVideo($data);
                                    break;
                                case 'voice':
                                    Request::sendVoice($data);
                                    break;
                                case 'photo':
                                    Request::sendPhoto($data);
                                    break;
                                case 'sticker':
                                    Request::sendSticker($data);
                                    break;
                            }
                        }
                    }
                }
            }
        }
    }
}
