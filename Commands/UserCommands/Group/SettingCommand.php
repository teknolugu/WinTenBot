<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 10/09/2018
 * Time: 07.55
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use src\Handlers\MessageHandlers;
use src\Model\Group;
use src\Model\Settings;
use src\Utils\Converters;

class SettingCommand extends UserCommand
{
    protected $name = 'set';
    protected $description = 'Change settings for current group';
    protected $usage = '/set <param>';
    protected $version = '1.0.0';

    /**
     * Execute command
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $mHandler = new MessageHandlers($message);
        $chat_id = $message->getChat()->getId();
        $mssg_id = $message->getMessageId();
        $from_id = $message->getFrom()->getId();
        $pecah = explode(' ', $message->getText(true));

        $isAdmin = Group::isAdmin($from_id, $chat_id);
        $isSudoer = Group::isSudoer($from_id);
        if ($isAdmin || $isSudoer && !$message->getChat()->isPrivateChat()) {
            if ($pecah[0] != '') {
//                $text = Settings::inlineSetting([
//                    'chat_id' => $chat_id,
//                    'inline' => $pecah
//                ]);

                $pecah1 = explode('_', $pecah[0]); // enable_bot || disable_bot
                if (in_array($pecah1[0], ['enable', 'disable'])) {
                    $col = 'enable' . ltrim($pecah[0], $pecah1[0]); // enable_bot
                    $int = Converters::stringToInt($pecah[0]);

                    $text = Settings::saveNew([
                        $col => $int,
                        'chat_id' => $chat_id,
                    ], [
                        'chat_id' => $chat_id,
                    ]);
                } else {
                    $text = 'Parameter invalid';
                }
                return $mHandler->sendText($text);
            } else {
                $mHandler->deleteMessage();
                $mHandler->sendText('🔄 Loading settings..','-1');
                $btns = Settings::getForTombol(['chat_id' => $chat_id]);
                $btn_markup = [];
                $btns = array_map(null, ...$btns);
                foreach ($btns as $key => $val) {
                    $p = explode('_', $key);
                    $cek = Converters::intToEmoji($val);
                    $callback = ltrim($key, $p[0]);
                    $btn_text = str_replace('_', ' ', ltrim($callback, '_'));
                    $btn_markup[] = [
                        'text' => $cek . ' ' . ucfirst($btn_text),
                        'callback_data' => 'setting' . $callback
                    ];
                }

                $text = "⚙ Group settings for <b>" . $message->getChat()->getTitle() . '</b>' .
                    "\n<i>Click for enable/disable</i>";
                $group_data = Settings::getNew(['chat_id' => $chat_id]);
                $r = $mHandler->editText($text, null, $btn_markup);
                $mHandler->deleteMessage($group_data[0]['last_setting_message_id']);
                Settings::saveNew([
                    'last_setting_message_id' => $r->result->message_id,
                    'chat_id' => $chat_id,
                ], [
                    'chat_id' => $chat_id,
                ]);
            }
        }

        return $r;
    }
}
