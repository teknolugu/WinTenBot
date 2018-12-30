<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 12/08/2018
 * Time: 10.04
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Kata;
use App\Waktu;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class ShortCommand extends UserCommand
{
    protected $name = 'short';
    protected $description = 'URL Shortener based Bitly API';
    protected $usage = '/short <url>';
    protected $version = '1.0.0';
    /**
     * Execute command
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $mssg_id = $message->getMessageId();
        $pecah = explode(' ', $message->getText());
        $repMssg = $message->getReplyToMessage();

        $time = $message->getDate();

        if ($repMssg != null) {
            if ($pecah[1] == 'all') {
                $links = '';
                $inEntity = '';
                $num = 1;
                $linkArr = Kata::extrlinkArr($repMssg->getText());
                $inEntities = json_decode($repMssg->getEntities(), true);
                foreach ($inEntities as $inE) {
                    if ($inE['type'] == 'text_link') {
                        $inEntity .= $inE['url'] . " Aa\n";
                    }
                }

                foreach ($linkArr as $link) {
                    $link = Kata::addhttp($link);
                    $result = json_decode(file_get_contents("http://api.bit.ly/v3/shorten?login=" .
                        bitly_username . "&apiKey=" . bitly_token . "&longUrl=" . urlencode($link) .
                        "&format=json"))->data;
                    $links .= $num . '. ' . $result->url . ' | ' . $result->long_url . "\n";
                    $num++;
                }
                $text = "<b>Short all </b>\n" . $links;
            } else if (is_numeric($pecah[1])) {
                $url = Kata::extrlink($repMssg->getText(), $pecah[1] ?? '0');
                $result = json_decode(file_get_contents("http://api.bit.ly/v3/shorten?login=" .
                    bitly_username . "&apiKey=" . bitly_token . "&longUrl=" . urlencode($url) .
                    "&format=json"))->data;
                $text = "URL Index ke " . $pecah[1] . "\n" .
                    "<b>Original : </b>" . $result->long_url . "\n" .
                    "<b>Shorten : </b>" . $result->url;
            }
        } else {
            $url = Kata::addhttp($pecah[1]);
            $result = json_decode(file_get_contents("http://api.bit.ly/v3/shorten?login=" .
                bitly_username . "&apiKey=" . bitly_token . "&longUrl=" . urlencode($url) .
                "&format=json"));

            if ($result->status_code == '200') {
                $text = "<b>Original : </b>" . $result->data->long_url .
                    "\n<b>Shorten : </b>" . $result->data->url;
            } else {
                $text = "<b>Error      : </b>" . $result->status_code .
                    "\n<b>Message : </b>" . $result->status_txt;
            }
        }

        $time = Waktu::jeda($time);
        $data = [
            'chat_id' => $chat_id,
            'text' => $text . $time,
            'reply_to_message_id' => $mssg_id,
            'parse_mode' => 'HTML'
        ];

        return Request::sendMessage($data);
    }
}
