<?php
/**
 * Created by PhpStorm.
 * User: azhe403
 * Date: 28/08/18
 * Time: 21:07
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use src\Handlers\ChatHandler;
use src\Model\Group;
use src\Model\Wordlists;
use src\Utils\Words;

class KataCommand extends UserCommand
{
    protected $name = 'kata';
    protected $description = 'Add word to blacklist or whitelist';
    protected $usage = '<kata>';
    protected $version = '1.0.0';

    public function execute()
    {
        $message = $this->getMessage();
	    $chatHandler = new ChatHandler($message);
        $chat_id = $message->getChat()->getId();
        $mssg_id = $message->getMessageId();
        $from_id = $message->getFrom()->getId();

        $pecah = explode(' ', $message->getText());
        if (Group::isSudoer($from_id)) {
//            $chatHandler->deleteMessage();
	        $chatHandler->sendText('🔄 Executing..', '-1');
            $validExec = ['blok', 'biar'];
            if (in_array($pecah[1], $validExec)) {
                $katas = [
	                'word'        => Words::clearAlphaNum(strtolower($pecah[2])),
	                'class'       => strtolower($pecah[1]),
	                'id_telegram' => $message->getFrom()->getId(),
	                'id_grup'     => $chat_id
                ];
                $blok = Wordlists::addWords($katas);
                if ($blok->rowCount() > 0) {
	                $chatHandler->editText('✍ Writing to cache..');
                    $wordlists = Wordlists::getAll();
                    $json = json_encode($wordlists);
                    file_put_contents(botData . 'wordlists.json', $json);
	                $text = '✅ Kata berhasil di tambahkan';
                } else {
	                $text = '⚠ Kata sudah ada dan tidak dapat di perbarui';
                }
            } else if ($pecah[1] == 'del') {
                $del = Wordlists::delTags(['word' => $pecah[2]]);
                if ($del->rowCount() > 0) {
	                $text = '✅ Kata berhasil di hapus';
                } else {
	                $text = '⚠ Kata tidak dapat di hapus atau tidak ada';
                }
            } else if ($pecah[1] == 'all') {
	            $chatHandler->editText('👓 Loading data..');
                $wordlists = Wordlists::getAll();
                $json = json_encode($wordlists);
	            $chatHandler->editText('✍ Writing to cache..');
                file_put_contents(botData . 'wordlists.json', $json);
	            $list = '';
                ksort($wordlists);
                $countWordlist = count($wordlists);
                foreach ($wordlists as $word) {
                    $list .= $word['word'] . ' -> ' . $word['class'] . "\n";
                }
                $text = "📜 <b>Wordlist</b>: <code>$countWordlist</code>\n" .
                    "===============================\n" .
                    trim($list);
            } else {
                $text = 'ℹ <b>Penggunaan /kata</b>' .
                    "\n<code>/kata [command] katamu</code>" .
                    "\n<b>Command : </b><code>blok, biar, del</code>";
            }
	        $chatHandler->editText($text);
        } else {
	        $chatHandler->sendText("⚠ <b>You isn't sudoer and can't use this feature.</b>");
        }
    }
}
