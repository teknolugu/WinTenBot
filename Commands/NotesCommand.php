<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 04/08/2018
 * Time: 23.33
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use GuzzleHttp\Client;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class NotesCommand extends UserCommand
{
	protected $name = 'spell';
	protected $description = 'Fix typo into corrected message';
	protected $usage = '/spell';
	protected $version = '1.0.0';
	
	/**
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 * @throws \Exception
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		
		$chat_id = $message->getChat()->getId();
		$mssg_id = $message->getMessageId();
		$repMssg = $message->getReplyToMessage();
		
		$cocot = explode(' ', $message->getText(true));
//        $client = new Client(['base_uri' => 'https://api.azhe.space']);
		
		$data = [
			'chat_id'    => $chat_id,
			'parse_mode' => 'HTML',
		];
		
		if ($repMssg != '') {
//            $response = $client->request('POST', 'notes', [
//                'form_params' => [
//                    'title' => $cocot[0],
//                    'content' => $repMssg->getText(),
//                    'id_chat' => $message->getChat()->getId(),
//                    'id_user' => $message->getFrom()->getId()
//                ],
//                'headers' => [
//                    'token' => new_token
//                ]
//            ]);
//
//            $result = $response->getBody();
//            $result = json_decode($result);
//            $text = "✅ Notes:\n" . $result->message;
			$mssg_id = $repMssg->getMessageId();
		} else {
			$inline_keyboard = new InlineKeyboard([
				['text' => 'inline current chat', 'switch_inline_query_current_chat' => true],
			], [
				['text' => 'whoami', 'callback_data' => 'whoami'],
				['text' => 'tags', 'callback_data' => 'tags'],
			]);
			
			$btn = [
				
				['text' => 'Start Search', 'switch_inline_query_current_chat' => true],
				['text' => 'Start Search', 'url' => 'http://a.b'],
			
			];

//            $response = $client->request('GET', 'notes', [
//                'headers' => [
//                    'token' => '9398923882377732'
//                ],
//                'query' => [
//                    'id_chat' => $message->getChat()->getId(),
//                ]
//            ]);
//
//            $result = $response->getBody();
//            file_put_contents('text.txt',$result,FILE_APPEND);
//            $result = json_encode($result);
			
			$data['reply_markup'] = $inline_keyboard;
			
			$text = 'Tags is <b>deprecated</b>. Please start search Notes in-Line bot';
		}
		
		$data['text'] = $text;
		
		return Request::sendMessage($data);
	}
}
