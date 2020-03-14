<?php

class Models_Task extends LMVC_ActiveRecord {

	public $tableName = "tasks";
	
	public $id				= "";
	public $task     	= "";
	public $projectId = "";
	public $urgent		= "";
	public $important	= "";
	public $duedate		= "";
		
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
		$sql = "SELECT p.id as projectId , p.projectName, t.id as id, t.task, t.urgent, t.important, t.duedate
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
				if(empty($postArray["duedate"])) {
					$sql = "UPDATE $this->tableName SET `duedate` = NULL WHERE id = $id";
		    	$result =  $this->query($sql);
				} else {
					$this->update();
				}
				die(json_encode($postArray));
		}
	}
	
	public function getTaskCount($projectId) {
		$sql = "SELECT COUNT(id) as taskCount
						FROM `tasks` as t
						WHERE t.projectId = ".$projectId;	
		$res = $this->findAll($sql,DB_FETCHMODE_ASSOC);
		return $res;
	}

	public function getDueDateData() {
		$sql = "SELECT u.id AS userId, u.name AS userName, u.email, p.id AS pId, p.projectname, t.id AS taskId, t.task, t.duedate 
						FROM tasks t 
						JOIN projects p ON t.projectId=p.id 
						JOIN users u ON p.userId=u.id 
						WHERE t.duedate = DATE(NOW())";
						
		$res = $this->findAll($sql,DB_FETCHMODE_ASSOC);
		return $res;
	}
	
	//TODO - write function to mark those tasks whose mail has been sent already to its user email

}
	

?>