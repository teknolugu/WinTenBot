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
use WinTenDev\Handlers\ChatHandler;
use WinTenDev\Model\Bot;

class TestCommand extends UserCommand
{
	protected $name = 'test';
	protected $description = 'Labs for feature update';
	protected $usage = '/test';
	protected $version = '1.0.0';
	
	/**
	 * Execute command
	 *
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function execute()
	{
		$mssg = $this->getMessage();
		$mssgText = $mssg->getText(true);
		$repMssg = $mssg->getReplyToMessage();
		$chatHandler = new ChatHandler($mssg);
		$chat_id = $mssg->getChat()->getId();
		$from_id = $mssg->getFrom()->getId();
		$pecah = explode(' ', $mssgText);

		$bot_username = Bot::getBotUsername();
		
		$chatHandler->sendText('Starting...');

		if($bot_username != "WinTenBetaBot"){
			return $chatHandler->editText("End");
		}


//		Logs::toSudoer([
//			'text' => 'test log',
//		]);

//		$btn_markup = [
//			['text' => 'Keluar grup', 'callback_data' => 'group_leave_' . $chat_id],
//		];
//		$text = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. ' .
//			'Dicta ipsa ipsam, iusto molestias nesciunt optio vero. ' .
//			'Autem dignissimos eius eum ipsa molestiae, ' .
//			'nam nobis provident quibusdam recusandae tenetur veniam voluptatem.';
//
//		$chatHandler->logToChannel($text, $btn_markup);
//		$a = '821871410';
//		$text = "<a href='tg://user?id=" . $a . "'>" . $a . '</a>';
		
//		if ($pecah[0] != '') {
//			$chatHandler->sendText('Pecah 0 -> ' . $pecah[0]);
//			$pecah2 = explode('/', $pecah[0]);
//			$username = '@' . $pecah2[3];
//			$getChat = Request::getChat(['chat_id' => $username]);
//			if ($getChat->isOk()) {
////				$chatId = \GuzzleHttp\json_decode($getChat->getResult(), true)['id'];
//				$chatId = $getChat->getResult()->id;
//				$text = $username . $chatId;
//			} else {
//				$text = $getChat->getResult();
//			}
//		}

//		$rawKeyboard = '';
//		$inlineKeyboardArr = $repMssg->getRawData()['reply_markup']['inline_keyboard'][0];
//		foreach($inlineKeyboardArr as $keyboard){
//			$rawKeyboard .= $keyboard['text'].'| '.$keyboard['url'].', ';
//		}
//
//		$text = $rawKeyboard;
//		$text = \GuzzleHttp\json_encode($pecah2,128);
//		$text = \GuzzleHttp\json_encode($repMssg->getRawData()['reply_markup']['inline_keyboard'][0],128);
//		$bot_username = $GLOBALS['bot_username'];
		$text = "I'm $bot_username";
		
		$chatHandler->editText($text, '-1');

//		$cache = new Cache([
//			'name'      => 'php-cache',
//			'path'      =>  botData.'php-cache/',
//			'extension' => '.json',
//		]);
//		$cache->setCache('chat');
//		$cache->store('test', $mssgText);

//		$cache->setCache($pecah[0]); // generate new file
//		$cache->store('hello', $pecah[1]); // store data string
//		$text = $cache->retrieve('hello');

//		Caches::write($chat_id, $pecah[0], $pecah[1]);
//
//		$text = Caches::read($chat_id, $pecah[0]);
//
//		$chatHandler->editText('Getting cache..');
//		return $chatHandler->editText($text);
	}
	
	function parseUrl($url)
	{
		$url_array = parse_url($url);
		$list = '';
		foreach ($url_array as $key => $val) {
			$list .= $key . ' ' . $val . "\n";
		}
		return trim($list);
	}
}
