<?php
abstract class LMVC_ActiveRecord
{
	private static $db;

	public $isEmpty;

	public $tableName;

	public $id	= 0;

	public $idField;

	private $error_list = array();

	private $tableInfo;

	private static $tableInfoCache;

	private $dbEventListeners = array();

	private $updatedRows = 0;

	private $deletedRows = 0;

	private $__currentAction = "";

	private $_listenerUpdateFields =  array();

	private $applicationIgnoreFields = array('db','isEmpty','tableName','error_list','idField','ignoreFieldList','applicationIgnoreFields','dbIgnoreFields','tableInfo','dbEventListeners','updatedRows','deletedRows', '__currentAction','tableInfoCache','_listenerUpdateFields', 'lazyLoadProperties','nullFields');

	private $ignoreFieldList = array();

	private $lazyLoadProperties = array();
	
	private $nullFields = array();


	final public function __construct($id=0)
	{

		self::$db = & LMVC_DB::getInstance()->getDB();

		if($this->tableName!=""){


			if(empty(self::$tableInfoCache)) self::$tableInfoCache = array();
			if(array_key_exists($this->tableName, self::$tableInfoCache))
			{
				$this->tableInfo = self::$tableInfoCache[$this->tableName];
			}
			else
			{
				$this->tableInfo = self::$db->tableInfo($this->tableName);
				self::$tableInfoCache[$this->tableName] = $this->tableInfo;

			}

			//$this->tableInfo = self::$db->tableInfo($this->tableName);
		}

		$this->isEmpty = true;

		if($id!=0){
			$this->fetchById($id);
		}

		$this->init();
	}

	abstract public function init();

	private function getter($property,&$value)
	{		
		
		if(method_exists($this, 'lazyLoadProperty'))
		{
			if(in_array($property, $this->lazyLoadProperties))
			{
				return $value = $this->lazyLoadProperty($property);
			}
			
		}
		
	}
	
	final public function addLazyLoadProperty($prop)
	{
		array_push($this->lazyLoadProperties, $prop);
	}


	final public function start_transaction()
	{
		self::$db->autoCommit(false);
	}

	final public function commit()
	{
		self::$db->commit();
		self::$db->autoCommit(true);
	}

	final public function rollback()
	{
		self::$db->rollback();
		self::$db->autoCommit(true);
	}


	public function __get($property)
	{
		$value=null;
		
		if(is_null($this->getter($property, $value)))
		{
			trigger_error(get_class($this) ." GET Error: Undefined property $property",E_USER_NOTICE);
		}	
		else
		{
			return $value;
		}	
	}

	public function __set($property,$value)
	{
		trigger_error( get_class($this) ." SET Error: Undefined property $property",E_USER_NOTICE);
	}
	
	public function __isset($property)
	{
	    
	    if(method_exists($this, 'lazyLoadProperty'))
	    {
	        if(in_array($property, $this->lazyLoadProperties))
	        {
	            return true;
	        }
	    }
	    
	    return false;
	   
	}

	public static function getDBObj()
	{
		return self::$db;
	}

	public function getDB()
	{
		return self::$db;
	}

	public function _getCurrentAction()
	{
		return $this->__currentAction;
	}

	private function getDbFieldType($fieldName)
	{
		foreach($this->tableInfo as $info){
			if($info['name'] == $fieldName){
				return $info['type'];
				break;
			}
		}
	}

	public function getPostData($ignore_list=array())
	{
		if($_SERVER['REQUEST_METHOD']=="POST"){
			$post_fields = $_POST;
			if(is_array($post_fields)){
				foreach($post_fields as $key => $val){
					if(is_array($ignore_list) && !in_array($key,$ignore_list)){
						if(property_exists($this, $key)){
							$this->{$key} = $val;
						}
					}
				}
			}
		}

	}


	public function objectsToArray($objects, $fields)
	{
		$data = array();
		foreach($objects as $object){
			$tmp = array();
			foreach($fields as $field){
				array_push($tmp,$object->{$field});
			}
			$data[] = $tmp;
			reset($fields);
		}
		return $data;

	}

	public function fieldList($object, $ignore_list=array(), $field_prefix=""){
		//@@TODO: ability to ignore desired properties from object.

		$fields = array_keys(get_object_vars($object));

		//do ignore list
		if(is_array($ignore_list)){
			foreach($ignore_list as $val){
				if(in_array($val,$fields)){
					array_splice($fields,array_search($val,$fields),1);

				}
			}
		}

		//do prefixing
		if($field_prefix!=""){
			foreach($fields as $key=> $field){
				$fields[$key] = $field_prefix .$field;
			}
		}


		return implode(",", $fields);
	}
	
	/**
	 * 
	 * Function allows you save selected fields as NULL in table.
	 * Important! - These fields should be allowed to store NULL values in DB. Failing to do so will cause an Exception.
	 * 
	 * @param array $fields: array of field names in which you want to set NULL value in DB
	 * 
	 */
	/*
	public function setNullFields($fields)
	{
		
		if(is_array($fields))
		{
			$this->nullFields = $fields;
		}
		else
		{
			trigger_error( get_class($this) ." Error: setNullFields function expects array of fields.", E_USER_ERROR);
		}
		
	}
	*/
	
	/**
	 *
	 * Function allows you save selected fields as NULL in table.
	 * Important! - These fields should be allowed to store NULL values in DB. Failing to do so will cause an Exception.
	 *
	 * @param array $fields: array of field names in which you want to set NULL value in DB
	 *
	 */
	public function addNullFields($fields)
	{
		
		if(is_array($fields))
		{
			foreach($fields as $field)
			{
				array_push($this->nullFields, $field);
			}
		}
		else
		{
			trigger_error( get_class($this) ." Error: addNullFields function expects array of fields.", E_USER_ERROR);
		}
		
	}	
	

	public function execute($tableName,$fields=array(),$mode=DB_AUTOQUERY_INSERT,$where=""){

		$field_values = array();

		$this->ignoreFieldList = array_merge($this->applicationIgnoreFields,$this->dbIgnoreFields);



		if(count($fields)==0)
		{
			$objectVars = get_object_vars($this);
			foreach($objectVars as $key=>$val){
				if(!in_array($key,$this->ignoreFieldList)){
						
					if(!is_null($this->{$key})){
						$field_values["`". $key ."`"]= stripslashes($this->{$key});
					}
						
				}
			}
		}
		else{
				
			foreach($fields as $val){
				$field_values["`". $val ."`"] = stripslashes($this->{$val});
			}
				
			//additional update fields registered from listeners (beforeUpdate)
			foreach($this->_listenerUpdateFields as $val){
				$field_values["`". $val ."`"] = stripslashes($this->{$val});
			}

		}
		
		//normally for update statements set NULL value in DB
		if(count($this->nullFields)>0)
		{
			foreach($this->nullFields as $field)
			{
				$key = "`". $field ."`";
				if(array_key_exists($key, $field_values))
				{
					$field_values[$key] = NULL;
				}
			}
		}

		
		
		$res = self::$db->autoExecute($tableName, $field_values, $mode,$where);

		if($mode==DB_AUTOQUERY_INSERT)
		{
			return self::$db->getOne("SELECT LAST_INSERT_ID()");
		}
		else
		{
			return self::$db->affectedRows();
		}
	}

	public static function insertMultiple($tableName,$fields,$data)
	{

		$field_list = implode(",", $fields);

		$value_list = rtrim(str_repeat("?,", count($fields)),",");

		$sql = "INSERT INTO $tableName ($field_list) VALUES (".  $value_list .")";

		$sth = self::$db->prepare($sql);

		$res = self::$db->executeMultiple($sth, $data);

		return true;

	}


	public function fetchById($id, $field_list = "*")
	{

		$this->__currentAction = "init";

		$sql = "SELECT $field_list FROM ". $this->tableName ." WHERE ". (empty($this->idField)?"id":$this->idField) ." = $id LIMIT 0,1";

		$res = $this->findAll($sql,DB_FETCHMODE_ASSOC);

		if(is_array($res) && count($res)>0)
		{
			list($index,$row)=each($res);
			$this->fillObjectVars($row);
			$this->isEmpty=false;
		}
	}

	public function fetchByProperty($property, $property_value, $field_list = "*")
	{

		$this->__currentAction = "init";


		$where_clause = "";

		if(is_string($property))
		{
			$where_clause = " $property = '$property_value'";
		}
		else if(is_array($property) && is_array($property_value))
		{
			$tmp = array();
			foreach($property as $key=>$prop)
			{
				array_push($tmp, "$prop = '". $property_value [$key] ."'");
			}
			if(count($tmp)>0)
			{
				$where_clause = implode(' AND ', $tmp);
			}
		}

		$sql = "SELECT $field_list FROM " . $this->tableName . " WHERE $where_clause LIMIT 0,1";

		//$sql = "SELECT $field_list FROM ". $this->tableName ." WHERE $property = '$property_value' LIMIT 0,1";
		//echo $sql;
		$res = $this->findAll($sql,DB_FETCHMODE_ASSOC);

		if(is_array($res) && count($res)>0){
			list($index,$row)=each($res);
			$this->fillObjectVars($row);
			$this->isEmpty=false;
		}

		$this->init();

	}

	private function fillObjectVars($row)
	{
		foreach($row as $key=>$val){
			if($val!==NULL){
				$fieldType = $this->getDbFieldType($key);
				if(in_array($fieldType,array("date","time","datetime"))){
					if($val!=""){
						$this->{$key} = $val;
					}
				}
				else{
					$this->{$key} = $val;
				}
			}
		}
	}

	public function getAll($_fields = array(), $filter= "", $order_by=array(), $offset=null, $limit=null,$distinct=false,$fetch_mode= DB_FETCHMODE_OBJECT)
	{

		if(empty($_fields))
		{
			$field_list = "*";
				
		}
		else
		{
			$field_list = implode(",",$_fields);
		}
		$sql = "SELECT ";
		if($distinct)
		{
			$sql .= " DISTINCT ";
		}
		$sql .= " $field_list FROM {$this->tableName} ";
			
		if(!empty($filter))
		{
			$sql .= " WHERE ". $filter;
		}

		if(!empty($order_by))
		{
			$orderByClause = " ORDER BY ";
			foreach($order_by as $key=>$val)
			{

				$orderByClause .= " $key  $val,";
			}
			$orderByClause = rtrim($orderByClause,",");
			$sql .= $orderByClause;
		}

		if(!empty($offset))
		{
			$sql .= " LIMIT ". $offset;
				
			if(!empty($limit))
			{
				$sql .= " $limit ";

			}
		}

		//return self::$db->getAll($sql,null,$fetch_mode);

		return self::findAll($sql,$fetch_mode);

	}


	public static function findAll($query,$fetch_mode= DB_FETCHMODE_OBJECT)
	{
		//  echo $query .";<br>";
		return self::$db->getAll($query,null,$fetch_mode);

	}

	public static function query($sql)
	{

		$result =  self::$db->query($sql);
		return $result;

	}

	public static function getOne($sql)
	{

		return self::$db->getOne($sql);

	}

	public static function getCol($sql,$col=0,$params = array())
	{
		return self::$db->getCol($sql,$col,$params);
	}


	public function executeSimple($tableName,$fields=array(),$mode=DB_AUTOQUERY_INSERT,$where="")
	{

		$res = self::$db->autoExecute($tableName, $fields, $mode,$where);
		if($mode==DB_AUTOQUERY_INSERT){
			return self::$db->getOne("SELECT LAST_INSERT_ID()");
		}
		else{
			return self::$db->updatedRows();
		}
	}



	final public function getDeletedRows()
	{
		return $this->deletedRows;
	}

	final public function getUpdatedRows()
	{
		return $this->updatedRows;
	}

	public function addListener($event, $call_back, $params=array())
	{
		$this->dbEventListeners[$event] = array('call_back'=>$call_back,'params'=>$params);

	}

	final protected function addListenerUpdateFields($field)
	{
		array_push($this->_listenerUpdateFields , $field);
	}


	final public function update($fields=array())
	{
		$this->__currentAction = "update";

		$result = $this->_notify('beforeUpdate');

		if($result)
		{
			$where_clause = "id=". $this->id;

			$this->updatedRows = $this->execute($this->tableName, $fields, DB_AUTOQUERY_UPDATE, $where_clause);
				
			$this->_notify('afterUpdate');
				
			return true;
				
		}

	}


	final public function create()
	{

		$this->__currentAction = "create";

		$result = $this->_notify('beforeCreate');

		if($result)
		{
			$fields = array();

			$this->id = $this->execute($this->tableName,$fields,DB_AUTOQUERY_INSERT);
				
			$this->_notify('afterCreate');
				
		}

		return $this->id;
	}


	final public function delete($whereClause = "")
	{

		$this->__currentAction = "delete";
			
		$deleteWhere = "";
		if($whereClause!="")
		{
			$deleteWhere = $whereClause;	//donot include KEYWORD WHERE
		}
		else
		{
			$deleteWhere = " id = ". $this->id;
		}

		$result = $this->_notify('beforeDelete');

		if($result)
		{
			$deleteStmt = "DELETE FROM ". $this->tableName . " WHERE " . $deleteWhere;
				
			$result = $this->query($deleteStmt);
				
			$this->deletedRows = self::$db->affectedRows();
				
			$this->_notify('afterDelete');
		}
	}


	private function _notify($event)
	{
		if(array_key_exists($event,$this->dbEventListeners))
		{
			$eventSubscriber = $this->dbEventListeners[$event];
			$params = $eventSubscriber['params'];
			return call_user_func_array($eventSubscriber['call_back'],$params);
		}
		else
		{
			return true;
		}
	}

	public function addError($errorMsg,$index=null)
	{
		if(empty($index))
		{
			array_push($this->error_list, $errorMsg);
		}
		else
		{
			if(isset($this->error_list[$index])){
				trigger_error("Error message already exists at specified index $index", E_USER_WARNING);
			}
			$this->error_list[$index] = $errorMsg;
		}
	}


	public function getErrors($index=null)
	{

		if(empty($index))
		{
			return $this->error_list;
		}
		else
		{
			if(!isset($this->error_list[$index])){
				trigger_error("Error message already exists at specified index $index", E_USER_WARNING);
			}
			else
			{
				return $this->error_list[$index];
			}
		}
	}

	public function hasErrors()
	{

		if(count($this->getErrors())==0){
			return false;
		}
		else
		{
			return true;
		}
	}

	public function isError($object)
	{
		if(PEAR::isError($object))
		{
			return true;
		}
		else
		{
			return false;
		}

	}

	public function __destruct()
	{
		// echo get_class($this) . " object destroyed<br/>\n";
	}

	public function destroy()
	{
		$vars = get_object_vars($this);
		foreach($vars as $varName=>$varValue)
		{
			//if variable is object of ActiveRecord Class call its destroy function too.
			if(is_object($this->{$varName}) && get_parent_class($this->{$varName}) == get_class())
			{
				$this->{$varName}->destroy();
			}

			unset($this->{$varName});
		}
	}
	
	public function refreshById($id)
	{
		$this->fetchById($id);
		$this->init();
	
	}
	
	public function refreshByProperty($property, $property_value, $field_list = "*")
	{
		$this->fetchByProperty($property, $property_value, $field_list = "*");
	}

}
?>