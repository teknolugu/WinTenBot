<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 06/08/2018
 * Time: 21.14
 */

namespace App;

class Kata
{
    public static function substrteks($text, $limit, $end = '...')
    {
        if (mb_strwidth($text, 'UTF-8') <= $limit) {
            return $text;
        }

        return rtrim(mb_strimwidth($text, 0, $limit, '', 'UTF-8')) . $end;
    }

    public static function substrkata($text, $maxchar, $end = '...')
    {
        if (strlen($text) > $maxchar || $text == '') {
            $words = preg_split('/\s/', $text);
            $output = '';
            $i = 0;
            while (1) {
                $length = strlen($output) + strlen($words[$i]);
                if ($length > $maxchar) {
                    break;
                } else {
                    $output .= ' ' . $words[$i];
                    ++$i;
                }
            }
            $output .= $end;
        } else {
            $output = $text;
        }
        return $output;
    }

    /**
     * @param $teks
     * @param int $index
     * @return mixed
     */
    public static function extrlink($teks, $index = 0)
    {
        $pattern = '~[a-z]+://\S+~';
        preg_match_all($pattern, $teks, $out);
        return $out[0][$index];
        //return explode(' ', strstr($teks, 'https://'))[$index];
    }

    public static function extrlinkArr($teks)
    {
        $pattern = '~[a-z]+://\S+~';
        preg_match_all($pattern, $teks, $out);
        return $out[0];
    }

    public static function addhttp($url)
    {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }
        return $url;
    }

    public static function shortUrl($url)
    {
        return json_decode(file_get_contents("http://api.bit.ly/v3/shorten?login=" .
            bitly_username . "&apiKey=" . bitly_token . "&longUrl=" . urlencode($url) .
            "&format=json"))->data->url;
    }
}
