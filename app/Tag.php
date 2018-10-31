<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 22/09/2018
 * Time: 16.01
 */

namespace App;

use GuzzleHttp\Client;

class Tag
{
    public static function tambahTag($datas)
    {
        $uri = winten_api . 'tag/?api_token=' . winten_key;
        $client = new Client();
        $response = $client->request('POST', $uri, [
            'form_params' => $datas
        ]);

        return $response->getBody();
    }

    public static function hapusTag($datas)
    {
        $uri = winten_api . 'tag/' . $datas['chat_id'] . '/' . $datas['tag'] . '?api_token=' . winten_key;
        $client = new Client();
        $response = $client->request('DELETE', $uri);
        return $response->getBody();
    }

    public static function ambilTag($datas)
    {
        $uri = winten_api . 'tag/' . $datas['chat_id'] . '/' . $datas['tag'] . '?api_token=' . winten_key;
        $client = new Client();
        $response = $client->request('GET', $uri);
        return $response->getBody();
    }

}
