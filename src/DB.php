<?php

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @since 0.0.1
 */
final class DB
{
	/**
	 * @var self
	 */
	private static $instance;
	
	/**
	 * @var \PDO
	 */
	private $pdo;
	
	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->pdo = new PDO(
			'mysql:host=' . db_data['server'] . ';dbname=' . db_data['database_name'],
			db_data['username'], db_data['password']
		);
	}
	
	/**
	 * @return \PDO
	 */
	public static function pdo()
	{
		return self::getInstance()->pdo;
	}
	
	/**
	 * @return self
	 */
	public static function getInstance()
	{
		if (self::$instance == null) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
