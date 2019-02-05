<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 03.27
 */

namespace src\Utils;

class Time
{
    /**
     * @param $time
     * @return Words
     */
    public static function jeda($time)
    {
        $time = microtime(true) - $_SERVER['request_time_float'] - $time;
        return "\n\n<code>⏱ " . number_format((float)$time, 3, '.', '') . ' s</code>';
    }

    public static function jedaNew($time)
    {
        $time = microtime(true) - $_SERVER['request_time_float'] - $time;
        return '<code>' . number_format((float)$time, 3, '.', '') . ' s</code>';
    }

    public static function sambuts()
    {
        $jam_now = date("H");
        if ($jam_now < 24 && $jam_now > 18) {
            $sambut = "selamat malam 🌙";
        } else if ($jam_now <= 18 && $jam_now >= 17) {
            $sambut = "selamat petang 🌥";
        } else if ($jam_now <= 17 && $jam_now >= 15) {
            $sambut = "selamat sore ⛅️";
        } else if ($jam_now <= 15 && $jam_now >= 12) {
            $sambut = "selamat siang ☀️";
        } else if ($jam_now <= 12 && $jam_now >= 4) {
            $sambut = "selamat pagi 🌤";
        } else if ($jam_now <= 4 && $jam_now >= 0) {
            $sambut = "selamat dini hari 🌚";
        }

        return $sambut;
    }

    public static function formatUnix($unixDate)
    {
        return date("d M Y H:i:s", $unixDate);
    }
}
