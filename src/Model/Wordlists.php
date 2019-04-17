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

class Wordlists
{
    public static function addWords($datas)
    {
        $db = new Medoo(db_data);
        $table = 'wordlists';
        $p = $db->count($table, ['word' => $datas['word']]);
        if ($p > 0) {
            $q = $db->update($table, $datas, ['word' => $datas['word']]);
        } else {
            $q = $db->insert($table, $datas);
        }

        return $q;
    }

    /**
     * @param $where
     * @return bool|\PDOStatement
     */
    public static function delTags($where)
    {
        $db = new Medoo(db_data);
        return $db->delete('wordlists', $where);
    }

    /**
     * @return array|bool
     */
    public static function getAll()
    {
        $db = new Medoo(db_data);
        return $db->select('wordlists', [
            'word', 'class'
        ], [
            'ORDER' => 'word'
        ]);
    }

    /**
     * @return mixed
     */
    public static function loadFromFile()
    {
        $filePath = botData . 'wordlists.json';
        $wordArray = \GuzzleHttp\json_decode(file_get_contents($filePath), true);
        return $wordArray;
    }

    /**
     * @param $pesan
     * @return bool
     */
    public static function isContainBadword($pesan)
    {
//        $wordlists = self::getAll();
        $wordlists = self::loadFromFile();
//        $apesan = explode(' ', $pesan);
        $apesan = Words::multiexplode([" ", "\n"], $pesan);
        foreach ($apesan as $anu) {
            foreach ($wordlists as $kata) {
                if (Words::cekKata($anu, $kata['word'])) {
                    return true;
                }
            }
        }
    }
}
