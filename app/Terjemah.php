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
     * @return string
     * @throws Exception
     */
    public static function Exe($text, $from, $to)
    {
        return TranslateClient::translate($from, $to, $text);
    }
}