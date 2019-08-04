<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 07/09/2018
 * Time: 19.02
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use src\Handlers\ChatHandler;
use src\Model\Group;
use src\Model\Settings;
use src\Utils\Words;

class WelcomeCommand extends UserCommand
{
    protected $name = 'welcome';
    protected $description = 'Set welcome message, buttons, others';
    protected $usage = '/welcome';
    protected $version = '1.0.0';
	
	protected $chatHandler;

    /**
     * Execute command
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
//        $mHandler = new ChatHandler($message);
	    $this->chatHandler = new ChatHandler($message);
        
        $chat_id = $message->getChat()->getId();
//		$mssg_id = $message->getMessageId();
        $from_id = $message->getFrom()->getId();

        $isAdmin = Group::isAdmin($from_id, $chat_id);
        $isSudoer = Group::isSudoer($from_id);
        if ($isAdmin || $isSudoer) {
            if (!$message->getChat()->isPrivateChat()) {
                $pecah = explode(' ', $message->getText(true));
	            $this->chatHandler->sendText('Loading data..');
                $commands = ['message', 'button'];
	            if (Words::isSameWith($pecah[0], $commands)) {
		            if ($pecah[1] == "reset") {
			            $welcome_data = "";
		            } else {
			            $welcome_data = trim(str_replace($pecah[0], '', $message->getText(true)));
		            }
	            	
		            $this->chatHandler->editText('Saving settings..');
                    $text = Settings::saveNew([
                        'welcome_' . $pecah[0] => $welcome_data,
                        'chat_id' => $chat_id,
                    ], [
                        'chat_id' => $chat_id
                    ]);
		            $this->updateCache();
		            $r = $this->chatHandler->editText('✅ Welcome ' . $pecah[0] . ' saved (y)');
                } elseif ($pecah[0] == '') {
                    $datas = Settings::getNew(['chat_id' => $chat_id]);
                    if ($datas[0]['welcome_message'] != '') {
                        $text = '<b>Welcome Message</b>' .
                            "\n<code>" . $datas[0]['welcome_message'] . '</code>';
                    } else {
                        $text = 'Tidak ada konfigurasi pesan welcome, pesan default akan di terapkan';
                    }

                    $btn_markup = [];
                    if ($datas[0]['welcome_button'] != '') {
                        $btn_data = $datas[0]['welcome_button'];
                        if ($pecah[0] != '-r') {
                            $btn_datas = explode(',', $btn_data);
                            foreach ($btn_datas as $key => $val) {
                                $btn_row = explode('|', $val);
                                $btn_markup[] = ['text' => $btn_row[0], 'url' => $btn_row[1]];
                            }
                        } else {
                            $text .= "\n\n<b>Button markup</b>\n" . $btn_data;
                        }
                    }
		            $this->updateCache();
		
		            $r = $this->chatHandler->editText($text, null, $btn_markup);
                } else {
                    $btn_markup = [
                        ['text' => 'Contoh Message', 'callback_data' => 'inbot-example_welcome-message-example'],
                        ['text' => 'Contoh Button', 'callback_data' => 'inbot-example_welcome-button-example'],
                    ];
		            $r = $this->chatHandler->editText('ℹ Parameter tidak valid.' .
                        "\nContoh:\n/welcome message pesan" .
                        "\n/welcome button text_tombol|link.com", '-1', $btn_markup);
                }
            } else {
	            $r = $this->chatHandler->sendText('Perintah /welcome hanya di dalam grup');
            }
        }

//		$r = $mHandler->editText($text, null, $btn_markup);
        return $r;
    }
	
	/**
	 *
	 */
	private function updateCache()
	{
		$this->chatHandler->editText("✍ Writing to cache..");
		$setting_data = Settings::getNew(['chat_id' => $this->chatHandler->getChatId()]);
		Settings::writeCache($this->chatHandler->getChatId(), $setting_data);
	}
}
