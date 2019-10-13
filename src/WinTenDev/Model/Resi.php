<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 1/20/2019
 * Time: 10:38 AM
 */

namespace WinTenDev\Model;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Resi
{
    /**
     * @param $kurir
     * @param $resi
     * @return string
     * @throws GuzzleException
     */
    public static function cekResi($kurir, $resi)
    {
//        $url = new_api . "/resi?kurir=$kurir&resi=$resi";
//        return file_get_contents($url);

        $client = new Client(['base_uri' => new_api]);
//        $client = new Client(['base_uri' => ians_api]);
        $response = $client->request('GET', 'resi', [
            'query' => [
                'kurir' => $kurir,
                'resi' => $resi
            ]
        ]);

        return $response->getBody();
    }
}
