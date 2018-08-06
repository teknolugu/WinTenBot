<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 03.27
 */

namespace App;

class Waktu
{
    /**
     * @param $time
     * @return string
     */
    public static function jeda($time)
    {
        $time = microtime(true) - $_SERVER['request_time_float'] - $time;
        return "\n\n<code>⏱ " . number_format((float)$time, 3, '.', '') . ' ms</code>';
    }
}
