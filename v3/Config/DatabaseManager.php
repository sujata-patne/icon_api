<?php

class Singleton{
	private static $instance = null;
	public function connection($dbName){
		if(self::$instance == null){
			self::$instance = new \mysqli(DBHOST, DBUSER, DBPASSWD, $dbName);
			
			if(self::$instance->connect_errno > 0){
				die('Unable to connect to database [' . self::$instance->connect_error . ']');
			}
		}
		return self::$connection;
	}
}	

class DatabaseManager extends Singleton{

	public function execute($query, $dbName){
		$result = mysqli_query($this->connection($dbName), $query);
		
		if( mysqli_num_rows($result) > 0 ){
			return $result;
		}else{
			return "No Record found!";
		}
	}
}

?>