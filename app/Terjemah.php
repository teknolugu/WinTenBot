<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 17.21
 */

namespace App;

use Exception;
use Stichoza\GoogleTranslate\TranslateClient;

class Terjemah
{
    /**
     * @param $text
     * @param $from
     * @param $to
     * @return array
     * @throws Exception
     */
    public static function Exe($text, $from, $to)
    {
        $tr = new TranslateClient(); // Default is from 'auto' to 'en'

        if ($to !== null) {
            $tr->setTarget($to); // Translate to
        } else {
            $tr->setSource(null);
            $tr->setTarget($from);
            $to = $from;
            $from = 'auto';
        }

        $result = [
            'from' => $from,
            'to' => $to,
            'text' => $tr->translate($text)
        ];

        return $result;
    }
}
