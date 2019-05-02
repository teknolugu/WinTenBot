<?php
/**
 * Created by IntelliJ IDEA.
 * User: Azhe
 * Date: 12/30/2018
 * Time: 6:07 PM
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use src\Handlers\MessageHandlers;

/**
 * User "/help" command
 *
 * Command that lists all available commands and displays them in User and Admin sections.
 */
class HelpCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'help';
    /**
     * @var string
     */
    protected $description = 'Show bot help commands help';
    /**
     * @var string
     */
    protected $usage = '/help or /help <command>';
    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $message = $this->getMessage();
	    $mHandler = new MessageHandlers($message);
        $chat_id = $message->getChat()->getId();
        $command_str = trim($message->getText(true));
        // Admin commands shouldn't be shown in group chats
	    $safe_to_show = true;

        list($all_commands, $user_commands, $admin_commands) = $this->getUserAdminCommands();
        // If no command parameter is passed, show the list.
        if ($command_str === '') {
            $text = '*Commands List*:' . PHP_EOL;
//            $r = Request::sendMessage($data);
            foreach ($user_commands as $user_command) {
                $text .= '/' . $user_command->getName() . ' - ' . $user_command->getDescription() . PHP_EOL;
//                $r = Request::editMessageText([
//                    'chat_id' => $chat_id,
//                    'message_id' => $r->result->message_id,
//                    'text'    => 'cek '.$text
//                ]);
            }
            if ($safe_to_show && count($admin_commands) > 0) {
                $text .= PHP_EOL . '*Admin Commands List*:' . PHP_EOL;
                foreach ($admin_commands as $admin_command) {
                    $text .= '/' . $admin_command->getName() . ' - ' . $admin_command->getDescription() . PHP_EOL;
                }
            }

            $text .= PHP_EOL . 'For exact command help type: /help <command>';
	
	        $tekt = 'Berikut adalah daftar perintah';
	        return $mHandler->sendText($tekt, null, BTN_HELP_HOME);
//            return Request::sendMessage([
//                'chat_id' => $chat_id,
//                'parse_mode' => 'markdown',
//                'text' => $text
//            ]);
        }
        $command_str = str_replace('/', '', $command_str);
        if (isset($all_commands[$command_str]) && ($safe_to_show || !$all_commands[$command_str]->isAdminCommand())) {
            $command = $all_commands[$command_str];
            $data['text'] = sprintf(
                'Command: %s (v%s)' . PHP_EOL .
                'Description: %s' . PHP_EOL .
                'Usage: %s',
                $command->getName(),
                $command->getVersion(),
                $command->getDescription(),
                $command->getUsage()
            );
            return Request::sendMessage($data);
        }
        $data['text'] = 'No help available: Command /' . $command_str . ' not found';
        return Request::sendMessage($data);
    }

    /**
     * Get all available User and Admin commands to display in the help list.
     *
     * @return Command[][]
     * @throws TelegramException
     */
    protected function getUserAdminCommands()
    {
        // Only get enabled Admin and User commands that are allowed to be shown.
        /** @var Command[] $commands */
        $commands = array_filter($this->telegram->getCommandsList(), function ($command) {
            /** @var Command $command */
            return !$command->isSystemCommand() && $command->showInHelp() && $command->isEnabled();
        });
        $user_commands = array_filter($commands, function ($command) {
            /** @var Command $command */
            return $command->isUserCommand();
        });
        $admin_commands = array_filter($commands, function ($command) {
            /** @var Command $command */
            return $command->isAdminCommand();
        });
        ksort($commands);
        ksort($user_commands);
        ksort($admin_commands);
        return [$commands, $user_commands, $admin_commands];
    }
}
