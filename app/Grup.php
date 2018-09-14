<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 10/09/2018
 * Time: 08.20
 */

namespace App;

use Longman\TelegramBot\Request;

class Grup
{
    public static function idAdmins($chatid)
    {
        $ids = [];
        $data = [
            'chat_id' => $chatid
        ];

        $result = Request::getChatAdministrators($data);
        $result = json_decode($result, true);
        if ($result['ok'] == 1) {
            foreach ($result['result'] as $admin) {
                $ids[] += $admin['user']['id'];
            }
        }

        return $ids;
    }

    public static function isAdmin($fromid, $chatid)
    {
        $ids = [];
        $data = [
            'chat_id' => $chatid
        ];

        $result = Request::getChatAdministrators($data);
        $result = json_decode($result, true);
        if ($result['ok'] == 1) {
            foreach ($result['result'] as $admin) {
                $ids[] += $admin['user']['id'];
            }
        }
        if (in_array($fromid, $ids)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isSudoer($fromid)
    {
        if (in_array($fromid, sudoer)) {
            return true;
        } else {
            return false;
        }
    }

    public static function simpanSet($datas)
    {
        $ch = curl_init();
        $url = winten_api . 'grupset/?api_token=' . winten_key;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datas));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        return $result;
    }
}
