<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 2/22/2019
 * Time: 4:46 PM
 */

namespace src\Utils;

class Converters
{
    /**
     * @param $int
     * @return string
     */
    public static function intToEmoji($int)
    {
        return $int == 1 ? '✅' : '❌';
    }

    /**
     * @param $string
     * @return integer
     */
    public static function stringToInt($string)
    {
        return $string == 'enable' ? 1 : 0;
    }

    /**
     * @param $emoji
     * @return int
     */
    public static function emojiToInt($emoji)
    {
        return $emoji == '✅' ? 1 : 0;
    }
}
