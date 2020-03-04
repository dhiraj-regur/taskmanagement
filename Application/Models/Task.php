<?php

class Models_Task extends LMVC_ActiveRecord {

	public $tableName = "tasks";
	
	public $id		= "";
	public $task     = "";
	public $projectId     = "";
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
	
	public function getProjectsAndTasks($userId) {
		$sql = "SELECT p.id as projectId , p.projectName, t.id as id, t.task, t.urgent, t.important
						FROM `tasks` as t
						RIGHT JOIN projects p
						ON t.projectId=p.id
						WHERE p.userId = ".$userId."
						ORDER BY p.id,t.id";	
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
		} else { //TODO - check we may not require create from here
		    foreach ($postArray as $value) {
		        $fields .= "'".$value."',";
		    }
				$fields = rtrim($fields, ',');
				
		    $sql = "INSERT INTO $this->tableName (id,task,projectId,urgent,important) VALUES ($fields)";
		    $result =  self::$db->query($sql);
		}
		//return response to textBox props
	}
	
	public function getTaskCount($projectId) {
		$sql = "SELECT COUNT(id) as taskCount
						FROM `tasks` as t
						WHERE t.projectId = ".$projectId;	
		$res = $this->findAll($sql,DB_FETCHMODE_ASSOC);
		return $res;
	}

}
	

?>