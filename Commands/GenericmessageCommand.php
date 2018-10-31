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

use App\Kata;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Commands\SystemCommand;

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
        $pesan = ltrim($this->getMessage()->getText(true), '!');
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $repMsg = $this->getMessage()->getReplyToMessage();
        if ($this->getMessage()) {
            $kata = strtolower($pesan);
            $pesanCmd = explode(' ', strtolower($pesan))[0];

            // Pindai kata
            if (Kata::isBadword($kata)) {
                $data = [
                    'chat_id' => $chat_id,
                    'message_id' => $message->getMessageId()
                ];

                Request::deleteMessage($data);
            }

            switch ($pesanCmd) {
                case 'ping':
                    return $this->telegram->executeCommand('ping');
                    break;
                case '@admin':
                    return $this->telegram->executeCommand('report');
                    break;
            }

            //Cek Makasih
            $makasih = Kata::cekKata($kata, thanks);
            if ($makasih) {
                $text = 'Sama-sama, senang bisa membantu gan...';
                Request::sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $text,
                    'reply_to_message_id' => $message->getMessageId(),
                    'parse_mode' => 'HTML'
                ]);
            }

            if ($repMsg !== null) {
                return $this->telegram->executeCommand('privatenotif');
            }
        }
    }
}

