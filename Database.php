<?php

class Database {

	// variables de conexion
	private static $db;
	private static $pdo;

	// constantes de configuracion
	const DATABASE = "db_mensajes_umt";
	const HOSTNAME = "localhost";
	const USERNAME = "root";
	const PASSWORD = "root";

	final private function __construct(){
		try{
			self::getDb();
		}catch(PDOException $e){
			//print $e;
		}
	}

	public static function getInstance(){
		if(self::$db==null){
			self::$db=new self();
		}
		return self::$db;
	}

	public function getDb(){
		if(self::$pdo==null){
			self::$pdo = new PDO(
					'mysql:dbname='.self::DATABASE.
					';host='.self::HOSTNAME.
					';port=3306;',
					self::USERNAME,self::PASSWORD,
					array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
				);

			self::$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}

		return self::$pdo;
	}

	final protected function __clone(){

	}

	function _destructor(){
		self::$pdo=null;
	}

}