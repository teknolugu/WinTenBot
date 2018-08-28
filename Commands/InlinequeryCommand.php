<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 8/24/2018
 * Time: 8:32 PM
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use App\Kata;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineQuery\InlineQueryResultArticle;
use Longman\TelegramBot\Entities\InputMessageContent\InputTextMessageContent;
use Longman\TelegramBot\Request;

class InlinequeryCommand extends SystemCommand
{
    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $inline_query = $this->getInlineQuery();
        $query = $inline_query->getQuery();
        $data = ['inline_query_id' => $inline_query->getId()];
        $query = trim($query);

        $url = winten_api . "pembaruan/$query?api_token=" . winten_key;
        $json = file_get_contents($url);
        $datas = json_decode($json, true);

        $results = [];

        $articles2 = [];
        foreach ($datas['message'] as $anu) {
            $url = "https://api.winten.tk/pembaruan/get/" . $anu['id'] . "?api_token=" . winten_key;

            $articles2[] = [
                'id' => $anu['id'],
                'title' => $anu['kb'] . ' - ' . $anu['tanggal'] . ' [' . $anu['ukuran'] . ']',
                'description' => "Versi : " . $anu['versi'] . ", Build : " . $anu['build'],
                'input_message_content' => new InputTextMessageContent(
                    [
                        'parse_mode' => 'HTML',
                        'message_text' =>
                            "<b>Versi \t\t : </b>" . $anu['versi'] .
                            "\n<b>Build \t : </b>" . $anu['build'] .
                            "\n<b>Ukuran \t : </b>" . $anu['ukuran'] .
                            "\n<b>Tanggal \t : </b>" . $anu['tanggal'] .
                            "\n<b>Unduh \t : </b><a href='$url'>Download</a>"
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
