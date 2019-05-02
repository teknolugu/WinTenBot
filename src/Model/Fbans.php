<?php
/**
 * Created by IntelliJ IDEA.
 * User: Azhe
 * Date: 1/18/2019
 * Time: 6:54 AM
 */

namespace src\Model;

use Medoo\Medoo;

class Fbans
{
    static private $table_name = 'fbans';
	/**
	 * @param $datas
	 * @return bool|\PDOStatement
	 */
	public static function saveFBans($datas)
	{
		$db = new Medoo(db_data);
        $p = $db->count( self::$table_name, ['user_id' => $datas['user_id']]);
        if ($p > 0) {
            $q = $db->update(self::$table_name, $datas, ['user_id' => $datas['user_id']]);
        } else {
            $q = $db->insert(self::$table_name, $datas);
        }
        return $q;
	}
	
	/**
	 * @return array|bool
	 */
	public static function spellText($text)
	{
		$db = new Medoo(db_data);
		$d = $db->select(self::$table_name, '*');
		foreach ($d as $e) {
			$text = str_replace($e['typo'], $e['fix'], $text);
		}
		return $text;
	}
	
	/**
	 * @return array|bool
	 */
	public static function getAll()
	{
		$db = new Medoo(db_data);
		return $db->select(self::$table_name, '*');
	}

    public static function deleteFban($where)
    {
        $db = new Medoo(db_data);
        return $db->delete(self::$table_name, $where);
    }

	public static function writeCacheFbans(){
        $fbansAll = \GuzzleHttp\json_encode(Fbans::getAll());
        file_put_contents(botData . 'cache-json/fbans-all.json', $fbansAll);
    }

    /*
     *Admin FBans
     */

    static protected $admin_table = 'fbans_admin';

    /**
     * @return array|bool
     */
    public static function getAdminFbansAll()
	{
        $db = new Medoo(db_data);
        return $db->select(self::$admin_table, '*');
	}
    /**
     * @param $datas
     * @return bool|\PDOStatement
     */
    public static function saveAdminFBans($datas)
    {
        $db = new Medoo(db_data);
        $p = $db->count( self::$admin_table, ['user_id' => $datas['user_id']]);
        if ($p > 0) {
            $q = $db->update(self::$admin_table, $datas, ['user_id' => $datas['user_id']]);
        } else {
            $q = $db->insert(self::$admin_table, $datas);
        }
        return $q;
    }

    public static function deleteAdminFban($where)
    {
        $db = new Medoo(db_data);
        return $db->delete(self::$admin_table, $where);
    }

    public static function writeCacheAdminFbans(){
        $fbansAll = \GuzzleHttp\json_encode(Fbans::getAdminFbansAll());
        file_put_contents(botData . 'cache-json/fbans-admin-all.json', $fbansAll);
    }

    /**
     * @param $user_id
     * @return bool
     */
    public static function isAdminFbans($user_id)
	{
	    $result = false;
        $allAdmins = self::getAdminFbansAll();
        foreach ($allAdmins as $e) {
            if($user_id == $e['user_id']){
                $result = true;
            }
        }
        return $result;
	}

    /**
     * @param $user_id
     * @return bool
     */
    public static function isBan($user_id){
        $result = false;
        $json = file_get_contents(botData.'cache-json/fbans-all.json');
//        $allFbans = self::getAll();
        $allFbans = \GuzzleHttp\json_decode($json, true);
        foreach ($allFbans as $e) {
            if($user_id == $e['user_id']){
                $result = true;
            }
        }
        return $result;
    }
}
