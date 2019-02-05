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

use App\Bot;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

/**
 * Callback query command
 *
 * This command handles all callback queries sent via inline keyboard buttons.
 *
 * @see InlineCommand.php
 */
class CallbackqueryCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'callbackquery';

    /**
     * @var string
     */
    protected $description = 'Reply to callback query';

    /**
     * @var string
     */
    protected $version = '1.1.1';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $callback_query = $this->getCallbackQuery();
        $callback_query_id = $callback_query->getId();
        $callback_data = $callback_query->getData();
        $callback_id = $callback_query->getMessage()->getMessageId();
        $callback_chat_id = $callback_query->getMessage()->getChat()->getId();
        $message = $callback_query->getMessage();
        $bacot = explode('_', $callback_data);

        $newText = '';
        $switch_element = mt_rand(0, 9) < 5 ? 'true' : 'false';
        $inline_keyboard = new InlineKeyboard([
            ['text' => 'inline', 'switch_inline_query' => $switch_element],
            ['text' => 'inline current chat', 'switch_inline_query_current_chat' => $switch_element],
        ], [
            ['text' => 'whoami', 'callback_data' => 'whoami'],
            ['text' => 'tags', 'callback_data' => 'tags'],
        ], [
            ['text' => 'jangkrik 1', 'callback_data' => 'jangkrik_1'],
            ['text' => 'jangkrik 2', 'callback_data' => 'jangkrik_2'],
            ['text' => 'jangkrik 3', 'callback_data' => 'jangkrik 3']
        ]);

        switch ($bacot[0]) {
            case 'start':
                switch ($bacot[1]) {
                    case 'terms':
                        $btn_data = array_chunk(BTN_TERMS_WITH_CALLBACK, 2);
                        switch ($bacot[2]) {
                            case 'eula':
                                $text = Bot::getTermsUse('eula');
                                If (isBeta) {
                                $text = str_replace('WinTen Bot', bot_name, $text);
                        }
                                break;
                            case 'opensource':
                                $text = bot_name . ' adalah Open Source';
                                break;
                        }

                        Request::editMessageText([
                            'chat_id'      => $callback_query->getMessage()->getChat()->getId(),
                            'message_id'   => $callback_id,
                            'parse_mode'   => 'HTML',
                            'reply_markup' => new InlineKeyboard([
                                'inline_keyboard' => $btn_data
                            ]),
                            'text'         => $text
                        ]);
                        break;
                }
                break;
        }

//        switch ($callback_data) {
//            case 'jangkrik_1':
//                $text = $callback_data . "\nJangkrik 1?";
//                Request::editMessageText([
//                    'chat_id'      => $callback_query->getMessage()->getChat()->getId(),
//                    'message_id'   => $callback_id,
//                    'reply_markup' => $inline_keyboard,
//                    'text'         => $text
//                ]);
//
//                break;
//
//            case 'jangkrik_2':
//                $text = $callback_data . "\njangkrik 2?";
//                Request::editMessageText([
//                    'chat_id'      => $callback_query->getMessage()->getChat()->getId(),
//                    'message_id'   => $callback_id,
//                    'reply_markup' => $inline_keyboard,
//                    'text'         => $text
//                ]);
//                break;
//
//            case 'whoami':
//                $text = "Siapa saia?\n" . $message->getFrom();
//                Request::editMessageText([
//                    'chat_id'      => $callback_query->getMessage()->getChat()->getId(),
//                    'message_id'   => $callback_id,
//                    'reply_markup' => $inline_keyboard,
//                    'text'         => $text
//                ]);
//                break;
//
//            case 'tags':
//                $url = 'https://api.winten.tk/tag/-1001387872546/hhh?api_token=1274';
//                $tags = file_get_contents($url);
//                $text = "Tags \n" . $tags;
//                Request::editMessageText([
//                    'chat_id'      => $callback_query->getMessage()->getChat()->getId(),
//                    'message_id'   => $callback_id,
//                    'reply_markup' => $inline_keyboard,
//                    'text'         => $text
//                ]);
//                break;
//
//
//            default:
//                $text = $callback_data;
//                Request::editMessageText([
//                    'chat_id'      => $callback_query->getMessage()->getChat()->getId(),
//                    'message_id'   => $callback_id,
//                    'reply_markup' => $inline_keyboard,
//                    'text'         => $text
//                ]);
//                break;
//        }

//        if ($newText != '') {
//            Request::editMessageText([
//                'chat_id'      => $callback_query->getMessage()->getChat()->getId(),
//                'message_id'   => $callback_id,
//                'reply_markup' => $inline_keyboard,
//                'text'         => $text
//            ]);
//        }

        $data = [
            'callback_query_id' => $callback_query_id,
            'text'              => $text . ' ' . $callback_id,
            'show_alert'        => true,
            'cache_time'        => 5,
        ];

//        return Request::answerCallbackQuery($data);
    }
}
