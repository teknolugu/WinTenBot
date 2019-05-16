<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use src\Handlers\ChatHandler;
use src\Handlers\MessageHandlers;
use src\Model\Fbans;
use src\Model\Group;
use src\Model\Settings;
use src\Model\UrlLists;
use src\Model\Wordlists;
use src\Utils\Words;

/**
 * Generic message command
 */
class GenericmessageCommand extends SystemCommand
{
    protected $name = 'genericmessage';
    protected $description = 'Handle generic message';
    protected $version = '1.0.0';

    /**
     * Execute command
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute()
    {
        $pesan = $this->getMessage()->getText();
        $message = $this->getMessage();
        $mHandler = new MessageHandlers($message);
        $chatHandler = new ChatHandler($message);
        $from_id = $message->getFrom()->getId();
        $from_first_name = $message->getFrom()->getFirstName();
        $from_last_name = $message->getFrom()->getLastName();
        $from_username = $message->getFrom()->getUsername();
        $chat_id = $message->getChat()->getId();
        $chat_username = $message->getChat()->getUsername();
        $chat_type = $message->getChat()->getType();
        $chat_title = $message->getChat()->getTitle();
        $repMsg = $this->getMessage()->getReplyToMessage();

        $kata = strtolower($pesan);
        $pesanCmd = explode(' ', strtolower($pesan))[0];

        // Pindai kata
        $forScan = $message->getText() ?? $message->getCaption();
        $wordScan = Words::clearAlphaNum($forScan);
        if (Wordlists::isContainBadword(strtolower($wordScan))) {
            $mHandler->deleteMessage();
        }

        // Scan url
        if(UrlLists::isContainBadUrl($forScan)){
            $mHandler->deleteMessage();
        }

        if(Fbans::isBan($from_id)){
            $text = "$from_id telah terdeteksi di " . federation_name;
            $kickRes = $chatHandler->kickMember($from_id, true);
            if($kickRes->isOk()){
                $text .= " dan berhasil di tendang";
            }else {
                $text .= " dan gagal di tendang, karena <b>" . $kickRes->getDescription()."</b>. ".
                "Pastikan saya Admin dengan level standard";
            }
            return $chatHandler->sendText($text,'-1');
        }

        // Perika apakah Aku harus keluar grup?
        if (isRestricted
            && !$message->getChat()->isPrivateChat()
            && Group::isMustLeft($message->getChat()->getId())) {
            $text = 'Sepertinya saya salah alamat. Saya pamit dulu..' .
                "\nGunakan @WinTenBot";
            $mHandler->sendText($text);
            return Request::leaveChat(['chat_id' => $chat_id]);
        }

        // Command Aliases
        switch ($pesanCmd) {
            case 'ping':
                return $this->telegram->executeCommand('ping');
                break;
            case '@admin':
                return $this->telegram->executeCommand('report');
                break;
	        case Words::isContain($pesan, '#'):
	            return $this->telegram->executeCommand('tags');
                break;
        }

        // Chatting
        switch (true) {
	        case Words::isSameWith($kata, 'gan'):
                $chat = 'ya gan, gimana';
                break;
	        case Words::isSameWith($kata, 'mau tanya'):
                $chat = 'Langsung aja tanya gan';
                break;
	        case Words::isContain($kata, thanks):
                $chat = 'Sama-sama, senang bisa membantu gan...';
                break;
	        case Words::isSameWith($kata, yuk):
                $chat = 'Ayuk, siapa takut 😂';
                break;

            default:
                break;
        }

        if ($from_username == '') {
            $group_data = Settings::getNew(['chat_id' => $chat_id]);
            if ($group_data[0]['enable_warn_username'] == 1) {
                $limit = $group_data[0]['warning_username_limit'];
	            $chat = "Hey, Segera pasang username ya, jangan lupa.";
//                    "\nPeringatan %2/$limit tersisa";
	            $btn_markup[] = ['text' => 'Pasang Username', 'url' => urlStart . 'username'];
                $mHandler->deleteMessage($group_data[0]['last_warning_username_message_id']);
                $r = $mHandler->sendText($chat, null, $btn_markup);
                Settings::saveNew([
                    'last_warning_username_message_id' => $r->result->message_id,
                    'chat_id' => $chat_id,
                ], [
                    'chat_id' => $chat_id,
                ]);
                return $r;
            }
        }

        $mHandler->sendText($chat, null, $btn_markup);

        if ($repMsg !== null) {
            if ($message->getChat()->getType() != "private") {
                $mssgLink = 'https://t.me/' . $chat_username . '/' . $message->getMessageId();
                $chat = "<a href='tg://user?id=" . $from_id . "'>" . $from_first_name . '</a>' .
                    " mereply <a href='" . $mssgLink . "'>pesan kamu" . '</a>' .
                    ' di grup <b>' . $chat_title . '</b>'
                    . "\n" . $message->getText();
                $data = [
                    'chat_id' => $repMsg->getFrom()->getId(),
                    'text' => $chat,
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true,
                ];

                if ($chat_username != '') {
                    $btn_markup = [['text' => '💬 Balas Pesan', 'url' => $mssgLink]];
                    $data['reply_markup'] = new InlineKeyboard([
                        'inline_keyboard' => array_chunk($btn_markup, 2),
                    ]);
                }

                return Request::sendMessage($data);
            } else {
                $chat_id = $repMsg->getCaptionEntities()[3]->getUrl();
                $chat_id = str_replace("tg://user?id=", "", $chat_id);

                $data = [
                    'chat_id' => $chat_id,
                    'text' => "lorem",
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true,
                ];

                return Request::sendMessage($data);
            }
        }

        $pinned_message = $message->getPinnedMessage()->getMessageId();
        if (isset($pinned_message)) {
            return $this->telegram->executeCommand('pinnedmessage');
        }
    }
}

