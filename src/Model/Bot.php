<?php
/**
 * Created by IntelliJ IDEA.
 * User: Azhe
 * Date: 12/16/2018
 * Time: 3:40 PM
 */

namespace src\Model;


class Bot
{
    /**
     * @param $data
     * @return false|string
     */
    public static function getTermsUse($data)
    {
        $text = botData . '/' . $data . ".html";
        $data = file_get_contents($text);
        return $data;
    }

    public static function setTermsUse($data)
    {
        $text = botData . '/term-use.html';
        $data = file_put_contents($text, $data);
        return $data;
    }

}
