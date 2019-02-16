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
	
	/**
	 * @param $time
	 * @return string
	 */
	public static function jedaNew($time)
    {
        $time = microtime(true) - $_SERVER['request_time_float'] - $time;
        return '<code>' . number_format((float)$time, 3, '.', '') . ' s</code>';
    }
	
	/**
	 * @return string
	 */
	public static function sambuts()
    {
	    $jam_now = date('H');
        if ($jam_now < 24 && $jam_now > 18) {
	        $sambut = 'malam 🌙';
        } else if ($jam_now <= 18 && $jam_now >= 17) {
	        $sambut = 'petang 🌥';
        } else if ($jam_now <= 17 && $jam_now >= 15) {
	        $sambut = 'sore ⛅';
        } else if ($jam_now <= 15 && $jam_now >= 12) {
	        $sambut = 'siang ☀';
        } else if ($jam_now <= 12 && $jam_now >= 4) {
	        $sambut = 'pagi 🌤';
        } else if ($jam_now <= 4 && $jam_now >= 0) {
	        $sambut = 'dini hari 🌚';
        }

        return $sambut;
    }
	
	/**
	 * @param $unixDate
	 * @return false|string
	 */
	public static function formatUnix($unixDate)
    {
	    return date('d M Y H:i:s', $unixDate);
    }
}
