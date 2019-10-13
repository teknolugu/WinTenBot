<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 10/09/2018
 * Time: 08.20
 */

namespace WinTenDev\Model;

use GuzzleHttp\Client;
use Longman\TelegramBot\Request;

class Group
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
	
	/**
	 * @param $id_chat
	 * @return bool
	 */
	public static function isMustLeft($id_chat)
    {
    	$isRestricted = $GLOBALS['is_restricted'];
	    return (!in_array($id_chat, restrictArea) && $isRestricted);
    }

    public static function simpanSet($datas)
    {
        $uri = winten_api . 'grupset/?api_token=' . winten_key;
        $client = new Client();
        $response = $client->request('POST', $uri, [
            'form_params' => $datas
        ]);

        return $response->getBody();

    }
}
