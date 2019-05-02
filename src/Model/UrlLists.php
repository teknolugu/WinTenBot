<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 2/23/2019
 * Time: 10:28 AM
 */

namespace src\Model;

use Medoo\Medoo;
use src\Utils\Words;

class UrlLists
{
    public static function addUrl($datas)
    {
        $db = new Medoo(db_data);
        $table = 'urllists';
        $p = $db->count($table, ['url' => $datas['url']]);
        if ($p > 0) {
            $q = $db->update($table, $datas, ['word' => $datas['url']]);
        } else {
            $q = $db->insert($table, $datas);
        }

        return $q;
    }

    /**
     * @param $where
     * @return bool|\PDOStatement
     */
    public static function deleteUrl($where)
    {
        $db = new Medoo(db_data);
        return $db->delete('urllists', $where);
    }

    /**
     * @return array|bool
     */
    public static function getAll()
    {
        $db = new Medoo(db_data);
        return $db->select('urllists', [
            'url', 'class'
        ], [
            'ORDER' => 'url'
        ]);
    }

    /**
     * @return mixed
     */
    public static function loadFromFile()
    {
        $filePath = botData . 'url-lists.json';
        $wordArray = \GuzzleHttp\json_decode(file_get_contents($filePath), true);
        return $wordArray;
    }

    /**
     * @param $pesan
     * @return bool
     */
    public static function isContainBadUrl($pesan)
    {
//        $wordlists = self::getAll();
        $wordlists = self::loadFromFile();
//        $apesan = explode(' ', $pesan);
        $apesan = Words::multiexplode([" ", "\n"], $pesan);
        foreach ($apesan as $anu) {
            foreach ($wordlists as $kata) {
                if (Words::cekKata($anu, $kata['url'])) {
                    return true;
                }
            }
        }
    }
}
