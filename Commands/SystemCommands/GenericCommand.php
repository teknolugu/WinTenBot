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
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use WinTenDev\Handlers\ChatHandler;

/**
 * Generic command
 *
 * Gets executed for generic commands, when no other appropriate one is found.
 */
class GenericCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'generic';

    /**
     * @var string
     */
    protected $description = 'Handles generic commands or is executed by default when a command is not found';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * Command execute method
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();

        //You can use $command as param
        $mHandler = new ChatHandler($message);
        $chat_id = $message->getChat()->getId();
        $user_id = $message->getFrom()->getId();
        $command = $message->getCommand();

        //If the user is an admin and the command is in the format "/whoisXYZ", call the /whois command
//        if (stripos($command, 'whois') === 0 && $this->telegram->isAdmin($user_id)) {
//            return $this->telegram->executeCommand('whois');
//        }

        // Commands Routing
        switch ($command) {
            case 'wik':
                return $mHandler->sendText('wik wik..');
                break;

            case 'notes':
                $this->telegram->executeCommand('tags');
                return $mHandler->sendText('Please use /tags next time..');
                break;

            case 'set':
                return $this->telegram->executeCommand('setting');
                break;
        }

//        $mHandler->sendText('Command /' . $command . ' not found.. :(' . "\nPlease call /help");

//        return Request::sendMessage($data);
    }
}
