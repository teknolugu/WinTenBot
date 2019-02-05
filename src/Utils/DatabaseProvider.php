<?php
/**
 * Created by IntelliJ IDEA.
 * User: Azhe
 * Date: 1/18/2019
 * Time: 6:50 AM
 */

namespace src\Utils;

use Medoo\Medoo;
use mysqli;

class DatabaseProvider
{
	protected $conn;
	
	/**
	 * @return Medoo
	 */
	
	public function makeInstance()
	{
		return new Medoo(db_data);
	}

//    public function init(){
//        try {
//            $this->conn = new PDO('mysql:host=' .db_data['server']. ';dbname=' .db_data['database_name'],
//                db_data['username'], db_data['password']);
//            // set the PDO error mode to exception
//            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//            echo 'Connected successfully';
//        }
//        catch(PDOException $e)
//        {
//            echo 'Connection failed: ' . $e->getMessage();
//        }
//
//        return $this->conn;
//    }

//    public function initSql(){
//        $this->conn = new mysqli(db_data['server'], db_data['username'], db_data['password'], db_data['database_name']);
//
//        return $this->conn;
//    }
}
