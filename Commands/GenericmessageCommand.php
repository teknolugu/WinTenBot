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
            $pesanCmd = strtolower($pesan);

            // Pindai kata
            if (Kata::isBadword($pesanCmd)) {
                $data = [
                    'chat_id' => $chat_id,
                    'message_id' => $message->getMessageId()
                ];

                Request::deleteMessage($data);
            }

            if ($pesanCmd === 'ping') {
                return $this->telegram->executeCommand('ping');
            }

            if ($repMsg !== null) {
                return $this->telegram->executeCommand('privatenotif');
            }
        }
    }
}

