<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 28/10/2018
 * Time: 10.07
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use src\Handlers\MessageHandlers;
use src\Model\Tags;
use src\Utils\Words;

class GetCommand extends UserCommand
{
	protected $name = 'get';
	protected $description = 'Get cloud tag, or #tag instead';
	protected $usage = '<tag>';
	protected $version = '1.0.0';
	
	/**
	 * Execute command
	 *
	 * @return void
	 * @throws TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$mHandler = new MessageHandlers($message);
		$chatid = $message->getChat()->getId();
		$fromid = $message->getFrom()->getId();
		$mssg_id = $message->getMessageId();
		$pecah = explode(' ', $message->getText());
		$repMssg = $message->getReplyToMessage();

		$limit = 1;
		if (Words::cekKandungan($message->getText(), '#')) {
			foreach ($pecah as $pecahan) {
			    if($limit <= 3) {
                    if (Words::cekKandungan($pecahan, '#')) {
                        $pecahan = ltrim($pecahan, '#');
                        $hashtag = Words::cekKandungan($pecahan, '#');
                        if (!$hashtag && strlen($pecahan) >= 3) {
                            $tag = Tags::getTags([
                                'id_chat' => $chatid,
                                'tag' => $pecahan,
                            ]);

                            if ($repMssg != null) {
                                $mssg_id = $repMssg->getMessageId();
                            }

                            $id_data = $tag[0]['id_data'];
                            $tipe_data = $tag[0]['type_data'];
                            $btn_data = $tag[0]['btn_data']; // teks1|link1.com, teks2|link2.com

                            $text = '#️⃣<code>#' . $tag[0]['tag'] . '</code>' .
                                "\n" . $tag[0]['content'];

                            $btns = [];
                            if ($btn_data != null) {
//							if ($pecah[1] != '-r') {
                                $abtn_data = explode(',', $btn_data); // teks1|link1.com teks2|link2.com
                                foreach ($abtn_data as $btn) {
                                    $abtn = explode('|', trim($btn));
                                    $btns[] = [
                                        'text' => trim($abtn[0]),
                                        'url' => trim($abtn[1]),
                                    ];
                                }
//
//							} else {
//								$text .= "\n" . $btn_data;
//							}
                            }

                            if ($tipe_data == 'text') {
                                $data['text'] = $text;
                                $mHandler->sendText($text, $mssg_id, $btns);
                            } else {
                                $data = [
                                    'chat_id' => $chatid,
                                    'parse_mode' => 'HTML',
                                    'reply_to_message_id' => $mssg_id,
                                    'disable_web_page_preview' => true,
                                    $tipe_data => $id_data,
                                    'caption' => $text,
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
                        $limit++;
                    }
                }else{
			        $mHandler->sendText("Due performance reason, we limit 3 batch call tags");
			        break;
                }
			}
		}
	}
}
