<?php

class Models_Task extends LMVC_ActiveRecord {

	public $tableName = "tasks";
	
	public $id		= "";
	public $task     = ""; 
	public $userId		= "";
	public $urgent		= "";
	public $important			= "";
		
	public $dbIgnoreFields = array('id');
	
	public function init()
	{
		 $this->addListener('beforeCreate', array($this,'doBeforeCreate'));	
		 $this->addListener('beforeUpdate', array($this,'doBeforeUpdate'));
	}
    

	
	protected function doBeforeCreate()
	{		
	    return $this->validate();
	}
	protected function doBeforeUpdate()
	{
	    return $this->validate();
	}
	
	public function validate() {
	  return !$this->hasErrors();
	}
	
	public function getTasks($userId) {
			$sql = "SELECT * FROM $this->tableName WHERE userId = $userId ORDER BY id"; //will not work after userId removal and projectId insert
			$sql = "SELECT * FROM $this->tableName WHERE projectId = $userId ORDER BY id"; //but this is wrong way
			$res = $this->findAll($sql,DB_FETCHMODE_ASSOC);
			return $res;
	}

	public function updateTask($postArray) {
		$id = $this->id;
		$sql = "SELECT id FROM ". $this->tableName ." WHERE id = $id";
		$fields ='';
		$res = $this->findAll($sql,DB_FETCHMODE_ASSOC); 
		if($res && empty($postArray['task'])) {
			$this->delete("id = ".$id);
		} elseif($res) { //update
				$this->update();
				die(json_encode($postArray));
		} else { //create
		    foreach ($postArray as $value) {
		        $fields .= "'".$value."',";
		    }
		    $fields = rtrim($fields, ',');
		    
		    $sql = "INSERT INTO $this->tableName (id,task,userId,urgent,important) VALUES ($fields)";
		    $result =  self::$db->query($sql);
		}
		//return response to textBox props
	}
		
}
	

?>