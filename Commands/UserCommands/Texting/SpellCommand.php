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
use WinTenDev\Handlers\ChatHandler;
use WinTenDev\Model\Group;
use WinTenDev\Model\Spell;

class SpellCommand extends UserCommand
{
    protected $name = 'spell';
    protected $description = 'Fix typo into corrected message';
    protected $usage = '/spell';
    protected $version = '1.0.0';

    /**
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chatHandler = new ChatHandler($message);

        $repMssg = $message->getReplyToMessage();
        $data = explode(' ', $message->getText(true));

        if ($repMssg != '') {
            $needSpell = $repMssg->getText();
//			$chatHandler->deleteMessage();
            $chatHandler->sendText('🔄 Spelling..');
            $fixedMssg = Spell::spellText($needSpell);
            $mssg_id = $repMssg->getMessageId();
            $r = $chatHandler->editText("✅ <i>Mungkin</i> yang di maksud adalah:\n" .
                "<code>".$fixedMssg."</code>",'-1');
        } elseif (count($data) == 2) {
            $r = $chatHandler->sendText("🤔 Checking permission",'-1');
            $isSudoer = Group::isSudoer($message->getFrom()->getId());
            if ($isSudoer) {
                $datas = [
                    'typo' => $data[0],
                    'fix' => $data[1],
                    'chat_id' => $message->getChat()->getId(),
                    'user_id' => $message->getFrom()->getId(),
                ];

                $r = $chatHandler->editText("🏗 Adding spell",'-1');
                $result = Spell::addSpell($datas);
                if($result->rowCount() > 0){
                    $text = "✅ Spelling berhasil di tambahkan";
                }else{
                    $text = "⚠ Spelling sudah ada atau tidak dapat di perbarui";
                }
                $r = $chatHandler->editText($text);
            }
        } else {
            $r = $chatHandler->sendText('ℹ <i>Reply</i> pesan yang mau Spell');
        }

        return $r;
    }
}
