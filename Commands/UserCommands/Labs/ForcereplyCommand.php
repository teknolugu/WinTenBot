<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 2/24/2019
 * Time: 4:43 PM
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

/**
 * User "/forcereply" command
 *
 * Force a reply to a message.
 */
class ForcereplyCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'forcereply';
    /**
     * @var string
     */
    protected $description = 'Force reply with reply markup';
    /**
     * @var string
     */
    protected $usage = '/forcereply';
    /**
     * @var string
     */
    protected $version = '0.1.0';

    /**
     * Command execute method
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute()
    {
        $chat_id = $this->getMessage()->getChat()->getId();
        $data = [
            'chat_id' => $chat_id,
            'text' => 'Write something:',
            'reply_markup' => Keyboard::forceReply(),
        ];
        return Request::sendMessage($data);
    }
}
