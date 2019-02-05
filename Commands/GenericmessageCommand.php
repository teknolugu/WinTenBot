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

use src\Model\Group;
use src\Utils\Words;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

/**
 * Generic message command
 */
class GenericmessageCommand extends SystemCommand
{
    /**
     * @var Words
     */
    protected $name = 'genericmessage';

    /**
     * @var Words
     */
    protected $description = 'Handle generic message';

    /**
     * @var Words
     */
    protected $version = '1.0.0';

    /**
     * Execute command
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $pesan = $this->getMessage()->getText();
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $repMsg = $this->getMessage()->getReplyToMessage();
        if ($this->getMessage()) {
            $kata = strtolower($pesan);
            $pesanCmd = explode(' ', strtolower($pesan))[0];

            // Pindai kata
	        if (Words::isBadword($kata)) {
                $data = [
                    'chat_id'    => $chat_id,
                    'message_id' => $message->getMessageId()
                ];

                Request::deleteMessage($data);
            }

            // Perika apakah Aku harus keluar grup?
            if (isRestricted
                && !$message->getChat()->isPrivateChat()
	            && Group::isMustLeft($message->getChat()->getId())) {
                $text = 'Sepertinya saya salah alamat. Saya pamit dulu..' .
                    "\nGunakan @WinTenBot";
                Request::sendMessage([
                    'chat_id'    => $chat_id,
                    'text'       => $text,
                    'parse_mode' => 'HTML'
                ]);
                Request::leaveChat(['chat_id' => $chat_id]);
            }

            // Command Aliases
            switch ($pesanCmd) {
                case 'ping':
                    return $this->telegram->executeCommand('ping');
                    break;
                case 'notes':
                    return $this->telegram->executeCommand('tags');
                    break;
                case '@admin':
                    return $this->telegram->executeCommand('report');
                    break;
	            case Words::cekKandungan($pesan, '#'):
                    return $this->telegram->executeCommand('get');
                    break;
            }

            //Cek Makasih
	        $makasih = Words::cekKata($kata, thanks);
            if ($makasih) {
                $text = 'Sama-sama, senang bisa membantu gan...';
                Request::sendMessage([
                    'chat_id'             => $chat_id,
                    'text'                => $text,
                    'reply_to_message_id' => $message->getMessageId(),
                    'parse_mode'          => 'HTML'
                ]);
            }

            // Chatting
            $chat = '';
            switch (true){
	            case Words::cekKata($kata, 'gan'):
                    $chat = 'ya gan, gimana';
                    break;
	            case Words::cekKata($kata, 'mau tanya'):
                    $chat = 'Langsung aja tanya gan';
                    break;

                default:
                    break;
            }

            Request::sendMessage([
                'chat_id'             => $chat_id,
                'text'                => $chat,
                'reply_to_message_id' => $message->getMessageId(),
                'parse_mode'          => 'HTML'
            ]);

            if ($repMsg !== null) {
                if ($message->getChat()->getType() != "private") {
                    $text = "<a href='tg://user?id=" . $message->getFrom()->getId() . "'>" . $message->getFrom()->getFirstName() . '</a>' . ' mereply ' .
                        "<a href='https://t.me/" . $message->getChat()->getUsername() . '/' . $message->getMessageId() . "'>pesan kamu" . '</a>' .
                        ' di grup <b>' . $message->getChat()->getTitle() . '</b>';
                    $text .= "\n" . $message->getText();
                    $data = [
                        'chat_id' => $repMsg->getFrom()->getId(),
                        'text' => $text,
                        'parse_mode' => 'HTML',
                        'disable_web_page_preview' => true
                    ];

                    return Request::sendMessage($data);
                } else {
                    $chat_id = $repMsg->getCaptionEntities()[3]->getUrl();
                    $chat_id = str_replace("tg://user?id=", "", $chat_id);

                    $data = [
                        'chat_id' => $chat_id,
                        'text' => "lorem",
                        'parse_mode' => 'HTML',
                        'disable_web_page_preview' => true
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
}

