<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 11/25/2018
 * Time: 6:12 AM
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Grup;
use App\Waktu;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class PromoteCommand extends UserCommand
{
    protected $name = 'promote';
    protected $description = 'Promote chat member (bot must admin)';
    protected $usage = '/promote';
    protected $version = '1.0.0';

    /**
     * Execute command
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $promoteRes = null;
        $tindakan = '';
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $from_id = $message->getFrom()->getId();
        $repMssg = $message->getReplyToMessage();
        $pecah = explode(' ', $message->getText());

        $time = $message->getDate();
        $time1 = Waktu::jedaNew($time);

        $senderId = $message->getFrom()->getId();
        $promoteByName = trim($message->getFrom()->getFirstName() . ' ' . $message->getFrom()->getLastName());
        $promotedName = trim($repMssg->getFrom()->getFirstName() . ' ' . $repMssg->getFrom()->getLastName());

        $promote_data = [
            'chat_id'              => $chat_id,
            'can_change_info'      => false,
            'can_post_messages'    => false,
            'can_edit_messages'    => false,
            'can_delete_messages'  => true,
            'can_invite_users'     => true,
            'can_restrict_members' => true,
            'can_pin_messages'     => true,
            'can_promote_members'  => false
        ];

        $depromote_data = [
            'chat_id'              => $chat_id,
            'can_change_info'      => false,
            'can_post_messages'    => false,
            'can_edit_messages'    => false,
            'can_delete_messages'  => false,
            'can_invite_users'     => false,
            'can_restrict_members' => false,
            'can_pin_messages'     => false,
            'can_promote_members'  => false
        ];

        if (isset($repMssg)) {
            $isAdmin = Grup::isAdmin($from_id, $chat_id);
            $isSudoer = Grup::isSudoer($from_id);
            if ($isAdmin || $isSudoer) {
                if ($pecah[1] == '-d') {
                    $depromote_data['user_id'] = $repMssg->getFrom()->getId();
                    $promoteRes = Request::promoteChatMember($depromote_data);
                    $tindakan = 'depromote';
                } else {
                    $promote_data['user_id'] = $repMssg->getFrom()->getId();
                    $promoteRes = Request::promoteChatMember($promote_data);
                    $tindakan = 'promote';
                }
            }
        } else {
            if ($pecah[1] == '-d') {
                $depromote_data['user_id'] = $message->getFrom()->getId();
                $promoteRes = Request::promoteChatMember($depromote_data);
                $tindakan = 'depromote';
            } else {
                $promote_data['user_id'] = $message->getFrom()->getId();
                $promoteRes = Request::promoteChatMember($promote_data);
                $tindakan = 'depromote';
            }
        }

        if ($promoteRes->isOk()) {
            if ($tindakan == 'promote') {
                $text = "<a href='tg://user?id=" . $promote_data['user_id'] . "'>$promotedName</a> menjadi Admin " .
                    "\nDirecomendasikan oleh <a href='tg://user?id=$senderId'>" . $promoteByName . '</a>';
            } else {
                $text = "<a href='tg://user?id=" . $depromote_data['user_id'] . "'>$promotedName</a> tidak menjadi Admin " .
                    "\nDiturunkankan oleh <a href='tg://user?id=$senderId'>" . $promoteByName . '</a>';
            }
        } else {
            $text = '<b>🚫 Status : </b><code>' . $promoteRes->getDescription() . '.</code>';
        }

        $time2 = Waktu::jedaNew($time);
        $time = "\n\n ⏱ " . $time1 . ' | ⏳ ' . $time2;

        $data = [
            'chat_id'    => $chat_id,
            'text'       => $text . $time,
            'parse_mode' => 'HTML'
        ];

        return Request::sendMessage($data);
    }
}
