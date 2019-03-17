<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 07/09/2018
 * Time: 19.02
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use src\Handlers\MessageHandlers;
use src\Model\Resi;

class ResiCommand extends UserCommand
{
    protected $name = 'resi';
    protected $description = 'Cek resi';
    protected $usage = '/resi jne/jnt no_resi';
    protected $version = '1.0.0';

    /**
     * Execute command
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $mHandler = new MessageHandlers($message);
        $cocot = explode(' ', strtolower($message->getText(true)));

        if ($cocot[0] != '') {
//        $kurirs = ['jne', 'pos', 'jnt', 'jne'];
            $mHandler->sendText("🔍 Sedang mengecek, silakan tunggu..");
//            if (in_array($cocot[0], $kurirs)) {
//                $kurir = array_search($cocot[0], $kurirs);
//                $kurir = $kurirs[$kurir];

            $cek = json_decode(Resi::cekResi($cocot[0], $cocot[1]), true);
//            }
//            else if ($cocot[0] != '') {
//                foreach ($kurirs as $kurir) {
//                    $mHandler->editText('Cek kurir ' . ucfirst($kurir) . ' resi ' . $cocot[0]);
//                    $cek = json_decode(Resi::cekResi($kurir, $cocot[0]), true);
//                    $mHandler->editText('Cek kurir ' . ucfirst($kurir) . ' resi ' . $cocot[0] .
//                        "\nHasil : " . $cek->informasi_pengiriman->no_resi);
//                    if ($cek->informasi_pengiriman->no_resi != '') {
//                        $mHandler->editText("Ditemukn..");
//                        break;
//                    }
//                }
//                $kurir = $cocot[0];
//            }


            if (count($cek['informasi_pengiriman']) > 0) {
                $mHandler->editText("🔍 Saya menemukan resi, tunggu sebentar lagi..");
                sleep(1);
                $info = '';
                $outbond = '';
                $infoResi = $cek['informasi_pengiriman'];
                foreach ($infoResi as $key => $value) {
                    $info .= "\n<b>" . ucfirst(str_replace('_', ' ', $key)) . '</b> : <code>' .
                        str_replace(["\n", ':'], '', $value) . '</code>';
                }

                $outbonds = $cek['status_pengiriman']['outbond'];
                foreach ($outbonds as $key => $value) {
                    $outbond .= "\n<b>" . $key . "</b>: <code>" . $value . '</code>';
                }

                $text = '<b>Kurir :</b> ' . ucfirst($cocot[0]) . $info . "\n" . $outbond;
            } else {

                $text = 'No resi ' . $cocot[1] . ' tidak di temukan ' .
                    "\nPerika kembali kurir dan no_resi";
            }

//            $text = json_encode($cek, 128);
            return $mHandler->editText($text);
        } else {
            $text = 'ℹ <b>Parameter</b> tidak valid' .
                "\n<b>Example</b>" .
                "\n<code>/resi jne|jnt no_resi</code>";
//                "\n<code>/resi no_resi</code> - AutoFind kurir";
            return $mHandler->sendText($text);
        }
    }
}
