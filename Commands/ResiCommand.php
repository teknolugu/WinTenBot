<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 07/09/2018
 * Time: 19.02
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use src\Utils\Time;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use src\Model\Resi;

class ResiCommand extends UserCommand
{
    protected $name = 'resi';
    protected $description = 'Cek resi';
    protected $usage = '/resi';
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
        $time = $message->getDate();
        $cocot = explode(' ', $message->getText(true));

        $kurirs = ['jne', 'pos','jnt','jne'];
        $r = Request::sendMessage([
            'chat_id' => $chat_id,
            'text'    => 'Sedang mengecek, silakan tunggu..'
        ]);
        usleep(1000000);

        if (in_array($cocot[0], $kurirs)) {
            $kurir = array_search($cocot[0], $kurirs);
            $kurir = $kurirs[$kurir];

            $cek = json_decode(Resi::cekResi($kurir, $cocot[1]));
        } else if($cocot[0] !='') {
            foreach ($kurirs as $kurir) {
                $r = Request::editMessageText([
                    'chat_id' => $chat_id,
                    'message_id' => $r->result->message_id,
                    'text'    => 'Cek kurir '.ucfirst($kurir).' resi '.$cocot[0]
                ]);

                $cek = json_decode(Resi::cekResi($kurir, $cocot[0]));
                $r = Request::editMessageText([
                    'chat_id' => $chat_id,
                    'message_id' => $r->result->message_id,
                    'text'    => 'Cek kurir '.ucfirst($kurir).' resi '.$cocot[0].
                        "\nHasil : ".$cek->informasi_pengiriman->no_resi
                ]);
                if($cek->informasi_pengiriman->no_resi != ''){
                    break;
                }
            }

            $infoResi = $cek->informasi_pengiriman;
            foreach ($infoResi as $key => $value) {
                $info .= "\n<b>" . ucfirst(str_replace('_', ' ', $key)) . '</b> : <code>' .
                    str_replace(["\n", ':'], '', $value) . '</code>';
            }

            $outbonds = $cek->status_pengiriman->outbond;
            foreach ($outbonds as $key => $value) {
                $outbond .= "\n<b>" . $key . "</b>\n <code>" . $value . '</code>';
            }

            if($infoResi->informasi_pengiriman->no_resi != '') {
                $text = 'Kurir : ' . ucfirst($kurir) . $info . "\n" . $outbond;
            }else{
                $text = 'No resi tidak di temukan '.$infoResi->informasi_pengiriman->no_resi;
            }
        }else{
            $text = 'ℹ️ <b>Kurir tidak terdefinisi</b>';
        }
	
	    $time = Time::jeda($time);
        $data = [
            'chat_id'                  => $chat_id,
            'text'                     => $text . $time,
            'message_id' => $r->result->message_id,
            'reply_to_message_id'      => $mssg_id,
            'disable_web_page_preview' => true,
            'parse_mode'               => 'HTML'
        ];

        Request::editMessageText($data);
    }
}
