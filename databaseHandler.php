<?php

/********************DATABASEHANDLER********************/

//define these to match your database
	define("DATABASE","gameplaza");
	define("HOST","localhost");
	define("USERNAME","root");
	define("PASSWORD","");
	
	
//Handles all the database queries, actions
class DbHandler{

	
	private static $Db = null;

	private function construct__() {}

	//singleton model, gives the DbHandler instance
	public static function getInstance() {
			static $Db = null;
			if ($Db === null) {
				$Db = new DbHandler();
			}
			return ($Db);
		}
		
	//deletes from database with given tablename and statement (if($where = $param))
	function dbDelete($table,$where,$param){
		$db = new PDO('mysql:host='.HOST.'; dbname='.DATABASE.'; charset=utf8',USERNAME,PASSWORD);
		$query = "DELETE FROM ".$table." WHERE ".$where." = ?";
		$sQuery = $db->prepare($query);
		$sQuery->bindParam(1,$param);
		$sQuery->execute();
	}

	//changes user rights
	function makeMod($username){
		$db = new PDO('mysql:host='.HOST.'; dbname='.DATABASE.'; charset=utf8',USERNAME,PASSWORD);
		$query = "UPDATE users SET rights = 2 WHERE username = ?";
		$sQuery = $db->prepare($query);
		$sQuery->bindParam(1,$username);
		$sQuery->execute();
	}
	//Database reading function, reads the rows that fit with given parameters
	function readDb($table, $column, $where, $stmt){
		
	try{
		$columns = explode(",",$column);
		$db = new PDO('mysql:host='.HOST.'; dbname='.DATABASE.'; charset=utf8',USERNAME,PASSWORD);
		
		//constructing the query
		$query = "SELECT ".$columns[0];
		for($i=1; $i<sizeof($columns); $i++){
			$query = $query.",".$columns[$i];
		}
		$query = $query." FROM " .$table;

		if($where != ''){
			$query = $query." WHERE ".$where.':term';
			$sQuery = $db->prepare($query);
			$sQuery->bindValue(':term',$stmt,PDO::PARAM_STR);
		}else{
			$sQuery = $db->prepare($query);
		}
		//getting the data
		$sQuery->execute();
		$rows = $sQuery->fetchAll(PDO::FETCH_ASSOC);
		
		return $rows;
	}catch (PDOException $exRead) {
			   return($exRead->getMessage());
			}
	}
	//gets all the tables' names from the database
	function getThreads(){
		$db = new PDO('mysql:host='.HOST.'; dbname='.DATABASE.'; charset=utf8',USERNAME,PASSWORD);
		$result= $db->query("show tables");
		$rows = $result->fetchAll(PDO::FETCH_ASSOC);
		
		return $rows;
	}
	//creates a new table for a new thread
	function createThreadTable($table){
		$db = new PDO('mysql:host='.HOST.'; dbname='.DATABASE.'; charset=utf8',USERNAME,PASSWORD);
		$query =  "CREATE TABLE "."thread_".$table. " (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, posts LONGBLOB, user VARCHAR(30), rights INT(2), created TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";
		$stmt = $db->prepare($query);
		$stmt->execute();
	}

	//counts posts from threads (made for the chart)
	function getPostC(){
		$threads = array();
		$threads =$this->getThreadA();
		$postCount = array();
		for($i=0; $i<count($threads); $i++){
			$postCount[] = count($this->readDb('thread_'.$threads[$i],'*','',''));
		}
		return $postCount;
	}
	//gets all the threads' names from the database
	function getThreadA(){
		$db = new PDO('mysql:host='.HOST.'; dbname='.DATABASE.'; charset=utf8',USERNAME,PASSWORD);
		$result= $db->query("show tables");
		$rows = $result->fetchAll(PDO::FETCH_ASSOC);
		$threads = array();
		for($i=0; $i<count($rows); $i++){
			
			if(substr($rows[$i]['Tables_in_'.DATABASE],0,7)=='thread_'){
				
				$threads[] = substr($rows[$i]['Tables_in_'.DATABASE],7);
			}
			
		}
		return $threads;
	}

	//write into database, inserts given data to given table
	function writeDb($table, $column, $value){
		$db = new PDO('mysql:host='.HOST.'; dbname='.DATABASE.'; charset=utf8',USERNAME,PASSWORD);
		$query =  "INSERT INTO ".$table." (".$column;
		$values = explode(",",$value);
		
		$query = $query.") VALUES(?";
		 for($i = 0; $i<sizeof($values)-1; $i++) {
					$query = $query . ", ?";
				}
		$query = $query.")";
		$sQuery = $db->prepare($query);
		
		 for ($i = 0; $i < sizeof($values); $i++) {
					$sQuery->bindParam($i+1, $values[$i]);
				}

		$sQuery->execute();

	}
}

?>