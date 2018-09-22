<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 06/08/2018
 * Time: 21.14
 */

namespace App;

use GuzzleHttp\Client;

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
        $json = file_get_contents($file);
        return json_decode($json, true)['message'];
    }

    public static function allBadword()
    {
        $data = '';
        foreach (self::listBadword() as $kata) {
            $data .= '<code>' . $kata['kata'] . ' -> ' . $kata['kelas'] . '</code>, ';
        }
        return $data;
    }

    public static function tambahKata($datas)
    {
        $uri = winten_api . 'kata/?api_token=' . winten_key;
        $client = new Client();
        $response = $client->request('POST', $uri, [
            'form_params' => $datas
        ]);

        return $response->getBody();
    }

    public static function hapusKata($kata)
    {
        $uri = winten_api . 'kata/' . $kata . '?api_token=' . winten_key;
        $client = new Client(['base_url' => $uri]);
        $response = $client->delete($uri);
        return $response->getBody();
    }

    public static function simpanJson()
    {
        $file = botData . 'badword.json';
        $url = winten_api . 'kata/?api_token=' . winten_key;
        $json = file_get_contents($url);
        file_put_contents($file, $json);
    }
}
