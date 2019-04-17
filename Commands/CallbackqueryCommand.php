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
use Longman\TelegramBot\Request;
use src\Handlers\MessageHandlers;
use src\Model\Bot;
use src\Model\Group;
use src\Model\Members;
use src\Model\Settings;
use src\Utils\Converters;

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
        $callback_from_id = $callback_query->getFrom()->getId();
        $callback_from_username = $callback_query->getFrom()->getUsername();
        $callback_mssg_text = $callback_query->getMessage()->getText();
        $callback_chat_title = $callback_query->getMessage()->getChat()->getTitle();
        $message = $callback_query->getMessage();
        $mHandler = new MessageHandlers($message);
        $bacot = explode('_', $callback_data);

        $newText = '';
//		$switch_element = mt_rand(0, 9) < 5 ? 'true' : 'false';
//		$inline_keyboard = new InlineKeyboard([
//			['text' => 'inline', 'switch_inline_query' => $switch_element],
//			['text' => 'inline current chat', 'switch_inline_query_current_chat' => $switch_element],
//		], [
//			['text' => 'whoami', 'callback_data' => 'whoami'],
//			['text' => 'tags', 'callback_data' => 'tags'],
//		], [
//			['text' => 'jangkrik 1', 'callback_data' => 'jangkrik_1'],
//			['text' => 'jangkrik 2', 'callback_data' => 'jangkrik_2'],
//			['text' => 'jangkrik 3', 'callback_data' => 'jangkrik 3'],
//		]);
//

        // SWITCT LEVEL 1
        switch ($bacot[0]) {
            // 1. level 1
            case 'start': // Start

                // SWITH LEVEL 2
                switch ($bacot[1]) {
                    // CASE LEVEL 2
                    case 'terms':
                        $btn_data = array_chunk(BTN_TERMS_WITH_CALLBACK, 2);

                        // SWITCH LEVEL 3
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
                            'chat_id' => $callback_query->getMessage()->getChat()->getId(),
                            'message_id' => $callback_id,
                            'parse_mode' => 'HTML',
                            'reply_markup' => new InlineKeyboard([
                                'inline_keyboard' => $btn_data,
                            ]),
                            'text' => $text,
                        ]);
                        break;
                }
                break; // End Start

            // 2. LEVEL 1
            case 'general':
//	        	$mHandler->editText('wik',null,BTN_OK_NO_CANCEL);
                Request::editMessageText([
                    'chat_id' => $callback_query->getMessage()->getChat()->getId(),
                    'message_id' => $callback_id,
                    'parse_mode' => 'HTML',
                    'reply_markup' => new InlineKeyboard([
                        'inline_keyboard' => array_chunk(BTN_OK_NO_CANCEL, 3),
                    ]),
                    'text' => $bacot[1],
                ]);
                break;

            // 3. Case HELP CALLBACK LEVEL 1
            case 'help':

                // SWITCH LEVEL 2
                switch ($bacot[1]) {
                    case 'group':
//						$mHandler->editText($bacot[1]);
                        $text = $bacot[1];
                        break;
                    case 'additional':
                        $text = $bacot[1];
//						$mHandler->editText($bacot[1], $callback_id);
                        break;
                    case 'security':
                        $text = $bacot[1];
//						$mHandler->editText($bacot[1], $callback_id);
                        break;
                    case 'about':
                        $text = $bacot[1];
//						$mHandler->editText($bacot[1], $callback_id);
                        break;
                }
                Request::editMessageText([
                    'chat_id' => $callback_query->getMessage()->getChat()->getId(),
                    'message_id' => $callback_id,
                    'parse_mode' => 'HTML',
                    'reply_markup' => new InlineKeyboard([
                        'inline_keyboard' => array_chunk(BTN_HELP_HOME, 2),
                    ]),
                    'text' => $text,
                ]);
                break;

            case 'verify':
//				$oldMessage = $callback_query->getMessage()->getText();
                $need_verif = ltrim($callback_data, 'verify_');
                $id_lists = explode(' ', $need_verif);
                if (in_array($callback_from_id, $id_lists)) {
                    foreach ($id_lists as $id) {
                        if ($id == $callback_from_id) {
//							$will_verif = 'IS_YOU_AND_';
                            Members::muteMember($callback_chat_id, $id, -1);
                            $text = 'Terima kasih sudah memverivikasi';
                        }
                    }
//					$will_verif .= 'IS_NEED_VERIFY';
                } else {
                    $text = 'IS_NOT_NEW_MEMBERS';
                }
//				$text = 'ListId: ' . $need_verif .
//					"\nFromID: " . $callback_from_id .
//					"\nChatID: " . $callback_chat_id .
//					"\nSTATUS: " . $will_verif;
//				Request::editMessageText([
//					'chat_id'      => $callback_query->getMessage()->getChat()->getId(),
//					'message_id'   => $callback_id,
//					'parse_mode'   => 'HTML',
//					'reply_markup' => new InlineKeyboard([
//						'inline_keyboard' => array_chunk(BTN_HELP_HOME, 2),
//					]),
//					'text'         => $oldMessage . 'Terima kasih ',
//				]);
                break;

            case 'setting':
                $isAdmin = Group::isAdmin($callback_from_id, $callback_chat_id);
                if ($isAdmin) {
                    Settings::toggleSetting([
                        'chat_id' => $callback_chat_id,
                        'toggle' => 'enable' . ltrim($callback_data, $bacot[0])
                    ]);

                    $text = "✅ Saved " . ucfirst(ltrim($callback_data, 'setting_'));
                    $edit = '⚙ Group settings for <b>' . $callback_chat_title . '</b>' . "\n\n" . $text;

                    $btns = Settings::getForTombol(['chat_id' => $callback_chat_id]);
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

                    Request::editMessageText([
                        'chat_id' => $callback_chat_id,
                        'message_id' => $callback_id,
                        'parse_mode' => 'HTML',
                        'reply_markup' => new InlineKeyboard([
                            'inline_keyboard' => array_chunk($btn_markup, 2),
                        ]),
                        'text' => $edit,
                    ]);
                } else {
                    $text = '401: Unauthorized.';
                }

                break;

            case 'check':
                if ($callback_from_username != "") {
                    $text = "Username kamu adalah: $callback_from_username";
                } else {
                    $text = "Kamu belum menetapkan Username. Silakan ikuti tutorial video tersebut";
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
            'text' => $text,
            'show_alert' => true,
            'cache_time' => 5,
        ];
        return Request::answerCallbackQuery($data);
    }
}
