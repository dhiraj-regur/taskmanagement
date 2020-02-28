<?php
/*
CLASS: Catz
VERSION: 3.0
MOD: 2010.06.18
*/
/*śćźżąłóć*/
require_once 'Zend/Db.php';
require_once 'Zend/Config.php';
abstract class Isd_Catz {
	public $db_coding = 'UTF-8';
	#instancja Lmvc bazy danych
	var $zend_db;
	public $c;
	function __construct() {
		$this->c =  new Zend_Config (
			array(
			'database' => array(
				'adapter' => 'Pdo_Mysql',
				'params' => array(
					'dbname' => DB_NAME,
					'username' => DB_UNAME,
					'password' => DB_PWD,
			'host' => DB_HOST,
			'profiler' => true
					)
				)
			)
		);
		//ee( self::$db, "DB");
		//ee($db->dns);
		
		$this->zend_db = Zend_Db::factory($this->c->database);
		$this->setCoding($this->db_coding);
	}
	
	public function setCoding($coding) {
		$sql  = "SET NAMES '".$coding."'";
		$this->zend_db->query($sql);	
	}
	
	#pobiera jeden rząd
	public function getOne($table, $fields, $by="", $keys=array()){
		$sql = 'SELECT '.$fields.' FROM '.$table.' '.$by.'';
		$set = $this->zend_db->fetchRow($sql, $keys);
		if(empty($set)) {
			return array();
		} else {
			return $set;	
		}
	}
	
	#pobiera dane wg prostego zapytnia, lub z określeniem trybu
	public function getAll($table, $fields, $by="", $keys=array()){
			$sql = 'SELECT '.$fields.' FROM '.$table.' '.$by.'';
//			echo $sql."<br>";
			$result = $this->zend_db->fetchAll($sql,  $keys);
			if(empty($result)) {
				return array();
			} else {
				return $result;	
			}
	}
	
	public function addOne($table, $data) {
		$n = $this->zend_db->insert($table, $data);	
		return $n;
	}
	
	public function addOneFilter($table, $data) {
		if(!empty($data) && is_array($data)) {
			foreach($data as $key=>$value) {
				$data[$key] =  $this->prepare_for_db($value);
			}
			return $this->zend_db->insert($table, $data);
		} else {
			return 0;
		}
	}
	
	public function updateOne($table, $data, $key, $value) {
		if(trim($value)=="") {
			return;
		}
		$condition = $key."='".$value."'";	
		$n = $this->zend_db->update($table, $data, $condition);
		return $n;
	}
	
	public function updateOneFilter($table, $data, $key, $value) {
		$condition = $key."='".$value."'";
		if(!empty($data) && is_array($data)) {
			foreach($data as $key=>$value) {
				$data[$key] =  $this->prepare_for_db($value);
			}
			return $this->zend_db->update($table, $data, $condition);
		} else {
			return 0;
		}
	}
	public function replaceOneFilter($table, $data, $key, $value) {
		if(!empty($data) && is_array($data)) {
			foreach($data as $key=>$value) {
				$data[$key] =  $this->prepare_for_db($value);
			}
			$check_set = $this->getOne($table, '*', "WHERE $key=?", array($value));
			if(empty($check_set)) {
				$n = $this->zend_db->insert($table, $data);
			} else {
				$condition = $key."='".$value."'";	
				$n = $this->zend_db->update($table, $data, $condition);
			}
			return $n;
		} else {
			return 0;
		}
	}
	
	public function deleteOne($table, $key, $value) {
		$condition = $key."='".$value."'";		
		$n = $this->zend_db->delete($table, $condition);
		return $n;
	}
	
	public function deleteAll($table, $condition="") {
		$n = $this->zend_db->delete($table,  $condition);
		return $n;
	}
	
	public function execute($query) {
		$result = $this->zend_db->fetchAll($query);
		if(!empty($result)) {
			return $result;
		} else {
			return array();
		}
	}
	
	public function executeSQL($sql) {
		return $this->zend_db->query($sql);
	}
	
	public function getLastId() {
		return $this->zend_db->lastInsertId();
	}
	
	public function showFields($table) {
		$sql = 'SHOW FIELDS FROM '.$table;
		$result = $this->zend_db->fetchAll($sql);
		return $result;
	}
	
	private function prepare_for_db($str) {
		if (get_magic_quotes_gpc() ) {
			$str = trim(strip_tags(htmlspecialchars(stripslashes($str))));
		} else {
			$str = trim(strip_tags(htmlspecialchars($str)));
		}
		return $str;
	}
		
	public function pfdb($str) {
		if (get_magic_quotes_gpc() ) {
			$str = trim(strip_tags(htmlspecialchars(stripslashes($str))));
		} else {
			$str = trim(strip_tags(htmlspecialchars($str)));
		}
		return $str;
	}
	
	public function pfdbs($str) {
		if (get_magic_quotes_gpc() ) {
			$str = trim(strip_tags(stripslashes($str)));
		} else {
			$str = trim(strip_tags($str));
		}
		return $str;
	}
	
	public function un_pfdb($str) {
		return stripslashes(html_entity_decode($str));
	} 
	
	public function un_pfdbs($str) {
		return htmlspecialchars($str);
	} 
}
?>