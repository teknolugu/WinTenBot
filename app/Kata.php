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

    public static function cekKata($pesans, $apa)
    {
        $apesan = explode(" ", $pesans);
        if (is_array($apa)) {
            foreach ($apa as $anu) {
                foreach ($apesan as $pesan) {
                    if ($pesan === $anu) {
                        return true;
                    }
                }
            }
        } else if ($pesans === $apa) {
            return true;
        }
    }

    public static function isBadword($pesan)
    {
        $apesan = explode(' ', $pesan);
        foreach ($apesan as $anu) {
            foreach (self::listBadword() as $kata) {
                if (self::cekKata($anu, $kata['kata'])) {
                    return true;
                }
            }
        }
    }

    public static function listBadword()
    {
        $file = botData . 'badword.json';
        $url = winten_api . 'kata/?api_token=' . winten_key;
        $json = file_get_contents($file);
        $datas = json_decode($json, true)['message'];
        return $datas;
    }

    public static function tambahKata($datas)
    {
        $ch = curl_init();
        $url = winten_api . 'kata/?api_token=' . winten_key;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datas));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        return $result;
    }

    public static function hapusKata($kata)
    {
        $ch = curl_init();
        $url = winten_api . 'kata/$kata?api_token=' . winten_key;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($ch);

        curl_close($ch);
        return $json;
    }

    public static function simpanJson()
    {
        $file = botData . 'badword.json';
        $url = winten_api . 'kata/?api_token=' . winten_key;
        $json = file_get_contents($url);
        file_put_contents($file, $json);
    }

}
