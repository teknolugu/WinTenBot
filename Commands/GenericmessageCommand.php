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

use App\Grup;
use App\Kata;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

/**
 * Generic message command
 */
class GenericmessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'genericmessage';

    /**
     * @var string
     */
    protected $description = 'Handle generic message';

    /**
     * @var string
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
            if (Kata::isBadword($kata)) {
                $data = [
                    'chat_id'    => $chat_id,
                    'message_id' => $message->getMessageId()
                ];

                Request::deleteMessage($data);
            }

            // Perika apakah Aku harus keluar grup?
            if (isRestricted
                && !$message->getChat()->isPrivateChat()
                && Grup::isMustLeft($message->getChat()->getId())) {
                $text = "Sepertinya saya salah alamat. Saya pamit dulu.." .
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
                case Kata::cekKandungan($pesan, '#'):
                    return $this->telegram->executeCommand('gettag');
                    break;
            }

            //Cek Makasih
            $makasih = Kata::cekKata($kata, thanks);
            if ($makasih) {
                $text = 'Sama-sama, senang bisa membantu gan...';
                Request::sendMessage([
                    'chat_id'             => $chat_id,
                    'text'                => $text,
                    'reply_to_message_id' => $message->getMessageId(),
                    'parse_mode'          => 'HTML'
                ]);
            }

            if ($repMsg !== null) {
                return $this->telegram->executeCommand('privatenotif');
            }

            $pinned_message = $message->getPinnedMessage()->getMessageId();
            if (isset($pinned_message)) {
                return $this->telegram->executeCommand('pinnedmessage');
            }
        }
    }
}

