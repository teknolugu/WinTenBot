<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 07/09/2018
 * Time: 19.02
 */

namespace Longman\TelegramBot\Commands\UserCommands;
;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use src\Handlers\MessageHandlers;
use src\Model\Bot;
use src\Model\Settings;
use src\Utils\Converters;
use src\Utils\Entities;
use src\Utils\Words;

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
		$mHandler = new MessageHandlers($mssg);
		$chat_id = $mssg->getChat()->getId();

//        $entity_data = null;
//        $entities = $repMssg->getEntities();
//        $text = $repMssg->getText();

//        $text = Kata::processEntities($text, $entities);
//        $entities = $this->genHtml();
//        $entities = Bot::getTermsUse();

//        $entities = KuttAPI::tambahTag([
//            'target' => $pecah[0]
//        ]);
//
//        $entities = json_encode($entities, true);

//        $entities = $pecah[0];

//        $cmds = json_encode($this->telegram->getCommandConfig("ping"));

//		$mHandler->sendText('lorem','');

//        $mHandler->deleteMessage();
		$mHandler->sendText('Initializing..');
//		sleep(1);
//		$mHandler->editText('Collecting data..', null, BTN_EXAMPLE);
//		$mHandler->editText('Calculating data..');
//		sleep(1);
//		$mHandler->editText('Processing..', null, BTN_EXAMPLE);
//		$mHandler->editText('Expanding all..');
//		$mHandler->editText('Processing 99999..');

//		$s = Spell::spellTest($mssgText);
//		$s = Spell::listSpell();
//		foreach ($s as $e) {
//			$mssgText = str_replace($e['typo'], $e['fix'], $mssgText);
//			$mHandler->editText("Spelling...\n" . $mssgText);
//		}

//		DB::insert('test', ['data' => 'lorem']);
//		$d = DB::insertOrUpdate('test', ['name' => 'Fulan 2'], ['data' => 'lorem']);
//		$d = Settings::saveNew([
//			'last_welcome_message_id' => 'lorem',
//			'chat_id' => 'asd'
//		], [
//			'chat_id' => 'asd',
//		]);
//		$d = Settings::getNew(['chat_id' => $mssg->getChat()->getId()]);
//		$welcome = $d[0]['welcome_message'];
//		$mHandler->editText('Finalizing..', null, BTN_EXAMPLE);
//		$mHandler->editText('F*cking finalizing..');
//		sleep(0.5);

//		for ($i = 0; $i <= 3; $i++) {
//			$mHandler->editText('test ' . $i);
//		}

//		$entities = $repMssg->getEntities();
//		$entitiesR = $repMssg->getRawData();
//		$text = $repMssg->getText();
//		$entities_count = count($repMssg->getEntities());
//		$html = '';
//		foreach ($entities as $k => $entity) {
//			if ($k === 0) {
//				$html .= mb_substr($text, 0, $entity->getOffset());
//			}
//
//			switch ($entity->getType()) {
//				default:
//					$html .= mb_substr($text, $entity->getOffset(), $entity->getLength());
//					break;
//				case 'mention':
//				case 'hashtag':
//				case 'cashtag':
//				case 'bot_command':
//				case 'url':
//				case 'email':
//				case 'phone_number':
//
//					$html .= mb_substr($text, $entity->getOffset(), $entity->getLength());
//
//					break;
//				case 'text_mention':
//
//					$html .= '<a href="tg://user?id=' . $entity->getUser()->getId() . '">' . mb_substr($text, $entity->getOffset(), $entity->getLength()) . '</a> ';
//
//					break;
//				case 'text_link':
//
//					$html .= '<a href="' . $entity->getUrl() . '">' . mb_substr($text, $entity->getOffset(), $entity->getLength()) . '</a> ';
//
//					break;
//
//				case 'bold':
//
//					$html .= '<b>' . mb_substr($text, $entity->getOffset(), $entity->getLength()) . '</b> ';
//
//					break;
//
//				case 'italic':
//
//					$html .= '<i>' . mb_substr($text, $entity->getOffset(), $entity->getLength()) . '</i> ';
//
//					break;
//				case 'code':
//
//					$html .= '<code>' . mb_substr($text, $entity->getOffset(), $entity->getLength()) . '</code> ';
//
//					break;
//				case 'pre':
//
//					$html .= '<pre>' . mb_substr($text, $entity->getOffset(), $entity->getLength()) . '</pre> ';
//
//					break;
//			}
//
//			if ($k === $entities_count) {
//				$html .= mb_substr($text, $entity->getOffset() + $entity->getLength());
//			}
////			$text = str_replace()
//
//		}

//		$welcome = $repMssg->getEntities();
//		$welcome = json_encode($entitiesR, JSON_PRETTY_PRINT);
//		$cot = '';
//		foreach ($entitiesR as $val) {
//			$cot .= $val['type'].' ';
//		}

//		$tes = Entities::toHtml($text, $entitiesR);

//		foreach ($entitiesR['entities'] as  $entity) {
//			if ($k === 0) {
//				$html = mb_substr($text, 0, $entity['offset']);
//			}

//			$potong = mb_substr($text, $entity['offset'], $entity['length']);
//			switch ($entity['type']) {
//				default:
//					$html = $potong;
//					break;
//				case 'mention':
//				case 'hashtag':
//				case 'cashtag':
//				case 'bot_command':
//				case 'url':
//				case 'email':
//				case 'phone_number':
//					$html = $potong;
//					break;
//				case 'text_mention':
//					$html = '<a href="tg://user?id=' . $entity['user']['id'] . '">' . $potong . '</a> ';
//					break;
//				case 'text_link':
//					$html = '<a href="' . $entity['url'] . '">' . $potong . '</a> ';
//					break;
//				case 'bold':
//					$html = '<b>' . $potong . '</b> ';
//					break;
//				case 'italic':
//					$html = '<i>' . $potong . '</i> ';
//					break;
//				case 'code':
//					$html = '<code>' . $potong . '</code> ';
//					break;
//				case 'pre':
//					$html = '<pre>' . $potong . '</pre> ';
//					break;
//			}

//			if ($k === $entities_count) {
//				$html = mb_substr($text, $entity['offset'] + $entity['length']);
//			}

//			$text = str_replace($potong, $html, $text);
//		}

//        $inline = $mssgText;
//        $pecah = explode('_', $inline); // enable_bot || disable_bot
//        $col = 'enable' . ltrim($inline, $pecah[0]); // enable_bot
//        $int = Converters::stringToInt($pecah[0]);
//        $tex = Settings::inlineSetting([
//            'chat_id' => $chat_id,
//            'inline' => $mssgText
//        ]);

//        $text = Settings::saveNew([
//            $col => $int,
//            'chat_id' => $chat_id,
//        ], [
//            'chat_id' => $chat_id,
//        ]);

//		$member_ids = '837237378, 218382173';
//		$btn_markup[] = ['text' => 'Verifikasi bahwa kamu manusya!', 'callback_data' => 'verify_' . $member_ids];

//        $col .= "\n" . $pecah[0] . ' ' . Converters::stringToInt($pecah[0]);

//        $text = Words::randomizeCase($mssgText);
//        $text = json_encode(parse_url($mssgText));
//        $text = $this->parseUrl($mssgText);
//        $text = Bot::loadInbotExample('welcome-message-example');
		$text = Entities::getHtmlFormatting($mssg);
		return $mHandler->editText($text);
	}
	
	function parseUrl($url)
	{
		$url_array = parse_url($url);
		$list = "";
		foreach ($url_array as $key => $val) {
			$list .= $key . " " . $val . "\n";
		}
		return trim($list);
	}
}
