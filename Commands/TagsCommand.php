<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 8/23/2018
 * Time: 10:45 AM
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use src\Model\Tags;
use src\Utils\Time;

class TagsCommand extends UserCommand
{
    protected $name = 'tags';
    protected $description = 'Get cloud tags in current chat';
    protected $usage = '/tags';
    protected $version = '1.0.0';
	
	/**
	 * Execute command
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $mssg_id = $message->getMessageId();

        $time = $message->getDate();
	    $time1 = Time::jedaNew($time);

//        $url = winten_api . "tag/$chat_id?api_token=" . winten_key;
//        $json = file_get_contents($url);
	    $json = Tags::getTags($chat_id);
	    $datas = json_decode($json, true)['result'];
	    if (count($datas['data']) > 0) {
		    $hit = count($datas['data']);
            $text = "#️⃣  <b>$hit Tags</b>\n-------\n";
		    foreach ($datas['data'] as $data) {
			    $text .= "<code>#" . $data['tag'] . '</code> ';
            }
        }
	
	    $time2 = Time::jedaNew($time);
        $time = "\n\n ⏱ " . $time1 . " | ⏳ " . $time2;

        $data = [
            'chat_id' => $chat_id,
            'text' => $text . $time,
            'reply_to_message_id' => $mssg_id,
            'parse_mode' => 'HTML'
        ];

        return Request::sendMessage($data);
    }
}
