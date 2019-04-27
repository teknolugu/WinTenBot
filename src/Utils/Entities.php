<?php
/**
 * Created by IntelliJ IDEA.
 * User: Azhe
 * Date: 12/22/2018
 * Time: 11:41 PM
 */

namespace src\Utils;

use Longman\TelegramBot\Entities\Message;

class Entities
{

    /**
     * @param $text
     * @param $entities
     * @return mixed
     */
    public static function toHtml($text, $entities)
    {
        $entities_count = count($entities);
        $text = 'lorem';
        foreach ($entities as $k => $entity) {
            if ($k === 0) {
                $html = mb_substr($text, 0, $entity['offset']);
            }

            $potong = mb_substr($text, $entity['offset'], $entity['length']);
            switch ($entity['type']) {
                default:
                    $html = $potong;
                    break;
                case 'mention':
                case 'hashtag':
                case 'cashtag':
                case 'bot_command':
                case 'url':
                case 'email':
                case 'phone_number':

                    $html = $potong;

                    break;
                case 'text_mention':

                    $html = '<a href="tg://user?id=' . $entity['user']['id'] . '">' . $potong . '</a> ';

                    break;
                case 'text_link':

                    $html = '<a href="' . $entity['url'] . '">' . $potong . '</a> ';

                    break;

                case 'bold':

                    $html = '<b>' . $potong . '</b> ';

                    break;

                case 'italic':

                    $html = '<i>' . $potong . '</i> ';

                    break;
                case 'code':

                    $html = '<code>' . $potong . '</code> ';

                    break;
                case 'pre':

                    $html = '<pre>' . $potong . '</pre> ';

                    break;
            }

            if ($k === $entities_count) {
                $html = mb_substr($text, $entity['offset'] + $entity['length']);
            }

            $text = str_replace($potong, $html, $text);

        }
        return $text;
    }

    public static function getHtmlFormatting(Message $message)
    {
        $repMssg = $message->getReplyToMessage();
        $entities = $repMssg->getEntities() ?? $repMssg->getCaptionEntities() ?? $message->getEntities() ?? $message->getCaptionEntities();
        $text = $repMssg->getText() ?? $repMssg->getCaption() ?? $message->getText() ?? $message->getCaption();
        $formatted = $text;
        foreach ($entities as $entity) {
            $needFormat = substr($text, $entity->getOffset(), $entity->getLength());
            switch ($entity->getType()) {
//                default:
//                case 'mention':
//                case 'hashtag':
//                case 'cashtag':
//                case 'bot_command':
//                case 'url':
//                case 'email':
//                case 'phone_number':
//                    $withFormat = mb_substr($text, $entity->getOffset(), $entity->getLength());
//                    break;

                case 'text_mention':
                    $withFormat = '<a href="tg://user?id=' . $entity->getUser()->getId() . '">' . $needFormat . '</a>';
                    $formatted = preg_replace("/\b$needFormat\b/", $withFormat, $formatted);
                    break;

                case 'text_link':
                    $withFormat = '<a href="' . $entity->getUrl() . '">' . $needFormat . '</a>';
                    $formatted = preg_replace("/\b$needFormat\b/", $withFormat, $formatted);
                    break;

                case 'bold':
                    $withFormat = '<b>' . $needFormat . '</b>';
                    $formatted = preg_replace("/\b$needFormat\b/", $withFormat, $formatted);
                    break;

                case 'italic':
                    $withFormat = '<i>' . $needFormat . '</i>';
                    $formatted = preg_replace("/\b$needFormat\b/", $withFormat, $formatted);

                    break;

                case 'code':
                    $withFormat = '<code>' . $needFormat . '</code>';
                    $formatted = preg_replace("/\b$needFormat\b/", $withFormat, $formatted);
                    break;

                case 'pre':
                    $withFormat = '<pre>' . $needFormat . '</pre>';
//                    $text = str_replace($needFormat, $withFormat, $text);
                    $formatted = preg_replace("/\b$needFormat\b/", $withFormat, $formatted);
                    break;
            }
        }
        return $formatted;

//        $html = '';
//        $entities_count = count($entities)-1;
//        foreach($entities as $k => $entity){
//
//            if($k === 0){
//
//                $html .= substr($text, 0, $entity->getOffset());
//
//            }
//
//            switch($entity->getType()){
//
//                default:
//                case 'mention':
//                case 'hashtag':
//                case 'cashtag':
//                case 'bot_command':
//                case 'url':
//                case 'email':
//                case 'phone_number':
//
//                    $html .= mb_substr($text, $entity->getOffset(), $entity->getLength());
//
//                    break;
//                case 'text_mention':
//
//                    $html .= '<a href="tg://user?id='.$entity->getUser()->getId().'">'.mb_substr($text, $entity->getOffset(), $entity->getLength()).'</a>';
//
//                    break;
//                case 'text_link':
//
//                    $html .= '<a href="'.$entity->getUrl().'">'.mb_substr($text, $entity->getOffset(), $entity->getLength()).'</a>';
//
//                    break;
//
//                case 'bold':
//
//                    $html .= '<b>' . mb_substr($text, $entity->getOffset(), $entity->getLength()) . '</b>';
//
//                    break;
//
//                case 'italic':
//
//                    $html .= '<i>' . mb_substr($text, $entity->getOffset(), $entity->getLength()) . '</i>';
//
//                    break;
//                case 'code':
//
//                    $html .= '<code>' . mb_substr($text, $entity->getOffset(), $entity->getLength()) . '</code>';
//
//                    break;
//                case 'pre':
//
//                    $html .= '<pre>' . mb_substr($text, $entity->getOffset(), $entity->getLength()) . '</pre>';
//
//                    break;
//
//            }
//
//            if($k === $entities_count){
//
//                $html .= mb_substr($text, $entity->getOffset() + $entity->getLength());
//
//            }
//
//        }
//        return $html;
    }
}
