<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 1/20/2019
 * Time: 10:38 AM
 */

namespace src\Model;

use GuzzleHttp\Client;

class Resi
{
    /**
     * @param $kurir
     * @param $resi
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function cekResi($kurir, $resi)
    {
//        $url = new_api . "/resi?kurir=$kurir&resi=$resi";
//        return file_get_contents($url);

        $client = new Client(['base_uri' => new_api]);
        $response = $client->request('GET', '/resi', [
            'query' => [
                'kurir' => $kurir,
                'resi' => $resi
            ]
        ]);

        return $response->getBody();
    }
}
