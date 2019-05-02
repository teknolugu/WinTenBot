<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 8/24/2018
 * Time: 8:32 PM
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use src\Utils\Words;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineQuery\InlineQueryResultArticle;
use Longman\TelegramBot\Entities\InputMessageContent\InputTextMessageContent;
use Longman\TelegramBot\Request;
use src\Model\Notes;

class InlinequeryCommand extends SystemCommand
{
	/**
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function execute()
	{
		$inline_query = $this->getInlineQuery();
		$query = $inline_query->getQuery();
		$data = ['inline_query_id' => $inline_query->getId()];
		$query = trim($query);
		
		$notes = Notes::getNotes('-1001387872546');
		$datas = json_decode($notes, true)['result']['data'];
		
		$results = [];
		$articles2 = [];
		foreach ($datas as $anu) {
//            $url = "https://api.winten.tk/pembaruan/get/" . $anu['id'] . "?api_token=" . winten_key;
			
			$articles2[] = [
				'id'                    => $anu['id'],
				'title'                 => $anu['title'],
				'description'           => $anu['content'],
				'input_message_content' => new InputTextMessageContent(
					[
//                        'parse_mode' => 'HTML',
						'message_text' =>
							"<b>Versi \t\t : </b>" . $anu['title'] .
							"\n<b>Build \t : </b>" . $anu['content']
//                            "\n<b>Ukuran \t : </b>" . $anu['ukuran'] .
//                            "\n<b>Tanggal \t : </b>" . $anu['tanggal'] .
//                            "\n<b>Unduh \t : </b><a href='$url'>Download</a>"
					]
				),
			];
		}
		
		foreach ($articles2 as $article) {
			$results[] = new InlineQueryResultArticle($article);
		}
		
		$data['results'] = '[' . implode(',', $results) . ']';
		
		return Request::answerInlineQuery($data);
	}
}
