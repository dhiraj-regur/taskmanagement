<?php

class Models_Project extends LMVC_ActiveRecord {

	public $tableName 	= "projects";
	public $id					= "";
	public $projectName = ""; 
	public $userId			= "";
		
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
	
	public function deleteProject($projectId) {
		global $db;
		$this->delete("id = ".$projectId);
		$deleteQuery = "DELETE FROM tasks WHERE projectId = $projectId";
		//$result =  self::$db->query($sql);
		$db->query($deleteQuery);
		die($projectId);
	}


	public function updateProject($postArray) {
		global $db;
		$id = $this->id;
		$sql = "SELECT id FROM ". $this->tableName ." WHERE id = $id";
		$fields ='';
		$res = $this->findAll($sql,DB_FETCHMODE_ASSOC);
		
		if($res) { //update
			$this->update();
			die(json_encode($postArray));
		} else {
		    die('update failed');
		}
	}
		
}
	

?>