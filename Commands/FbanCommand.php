<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 04/08/2018
 * Time: 23.33
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use src\Handlers\ChatHandler;
use src\Model\Fbans;
use src\Model\Group;
use src\Utils\Converters;

class FbanCommand extends UserCommand
{
    protected $name = 'fban';
    protected $description = 'Lets ban federation';
    protected $usage = '/fban';
    protected $version = '1.0.0';

    /**
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $from_id = $message->getFrom()->getId();
        $chat_id = $message->getChat()->getId();
        $chatHandler = new ChatHandler($message);
        $federation_name = "<b>WinTenDev Federation Circle</b>";
        $not_registered = $text = "⚠ Kamu belum teregistrasi ke " . federation_name .
            "\nKamu dapat register dengan <code>/fban register</code>" .
            "\n\n<b>Warning: </b> Fake reports might make you unable to become an FBan Admin forever!";

        $repMssg = $message->getReplyToMessage();
        $data = explode(' ', $message->getText(true));
        $r = $chatHandler->sendText("🤔 Checking permission..", '-1');
        $ignoreParams = ['register', 'all', 'admin-all'];
        if (Fbans::isAdminFbans($from_id) && !in_array($data[0], $ignoreParams)) {
            $r = $chatHandler->editText("🏗 Collecting data..");
            if ($repMssg != '') {
                $user_id = $repMssg->getFrom()->getId();
                $reason_ban = $message->getText(true);
                $banned_by = $message->getFrom()->getId();
            } elseif (count($data) == 2) {
                $user_id = $data[0];
                $reason_ban = str_replace($user_id, '', $message->getText(true));
                $banned_by = $message->getFrom()->getId();
            } else {
                $text = "ℹ $federation_name" .
                    "\n<code>/fban reason_ban</code> (InReply)" .
                    "\n<code>/fban user_id reason_ban</code> (InMessage)" .
                    "\n\n<b>Warning: </b> Fake reports might make you unable to become an FBan Admin forever!";
                return $chatHandler->editText($text);
            }

            if (Group::isAdmin($user_id, $chat_id)) {
                return $chatHandler->editText("Admin group can't be added to FBan");
            }

            $fbans_data = [
                'user_id' => $user_id,
                'reason_ban' => $reason_ban,
                'banned_by' => $banned_by,
                'banned_from' => $message->getChat()->getId(),
            ];

            $r = $chatHandler->editText("🏗 Adding to $federation_name");
            $fban = Fbans::saveFBans($fbans_data);
            if ($fban->rowCount() > 0) {
                $chatHandler->editText("✍ Writing to cache..");
                Fbans::writeCacheFbans();
                $text = "✅ <b>User</b> succesfully added to $federation_name";
            } else {
                $text = "✅ <b>User</b> failed to add or can't be updated";
            }
            return $chatHandler->editText($text);
        } else if (Group::isAdmin($from_id, $chat_id)) {
            if ($data[0] == "register") {
                $reg_fban = [
                    'user_id' => $from_id,
                    'promoted_from' => $chat_id
                ];

                $r = $chatHandler->editText("🏗 Adding to $federation_name");
                $result = Fbans::saveAdminFBans($reg_fban);
                if ($result->rowCount() > 0) {
                    $chatHandler->editText("✍ Writing to cache..");
                    Fbans::writeCacheFbans();
                    $text = "✅ <b>You</b> succesfully added to Admins $federation_name";
                } else {
                    $text = "✅ <b>You</b> already joined to $federation_name";
                }
                return $chatHandler->editText($text);
            } else if (Group::isSudoer($from_id)) {
                if ($data[0] == 'all') {
                    $r = $chatHandler->editText("🏗 Collecting data..");
                    $lists = "";
                    $fbans = Fbans::getAll();
                    $countFBans = count($fbans);
                    if ($countFBans > 0) {
                        foreach ($fbans as $fban) {
                            $lists .= $fban['user_id'] . ' by ' . $fban['banned_by'] . ' | ' . $fban['banned_from'] . "\n";
                        }
                    } else {
                        $lists = "No FBans";
                    }
                    $chatHandler->editText("✍ Writing to cache..");
                    Fbans::writeCacheFbans();
                    $text = "<b>Fbans Lists</b>: " . $countFBans .
                        "\n" . trim($lists);
                } else if ($data[0] == 'admin-all') {
                    $r = $chatHandler->editText("🏗 Collecting data..");
                    $lists = "";
                    $fbans = Fbans::getAdminFbansAll();
                    $countAdmin = count($fbans);
                    if ($countAdmin > 0) {
                        foreach ($fbans as $fban) {
                            $lists .= Converters::intToEmoji(!$fban['is_banned']) . ' ' . $fban['user_id'] .
                                ' from ' . $fban['promoted_from'] . "\n";
                        }
                    } else {
                        $lists = "No Admin FBans";
                    }
                    $chatHandler->editText("✍ Writing to cache..");
                    Fbans::writeCacheAdminFbans();
                    $text = "<b>Admin Fbans Lists</b>: " . $countAdmin .
                        "\n" . trim($lists);
                } else {
                    $text = $not_registered;
                }
                return $chatHandler->editText($text);
            } else {
                $text = $not_registered;
                $r = $chatHandler->editText($text);
            }
        } else {
            $r = $chatHandler->editText("⚠ You must Admin to use $federation_name");
        }

        return $r;
    }
}
