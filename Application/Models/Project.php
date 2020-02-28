<?php

class Models_Project extends LMVC_ActiveRecord {

	public $tableName = "projects";
	//public $usersTable = 'users';

	public $id		= "";
	public $projectName     = ""; 
	public $userId		= "";
		
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
	
	public function getProjects($userId) {
		global $db;
		$sql = "SELECT p.id , p.projectName, u.id as userId 
						FROM $this->tableName p INNER JOIN users u 
						ON p.userId=u.id 
						WHERE u.id = $userId 
						ORDER BY p.id";
		$result = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		return $result;
	}

	public function deleteProject($projectId) {
		global $db;
		$this->delete("id = ".$projectId);
		$deleteQuery = "DELETE FROM tasks WHERE projectId = $projectId";
		//$result =  self::$db->query($sql);
		$db->query($deleteQuery);
		die($projectId); //die('deleted');
	}


	public function updateProject($postArray) {
		global $db;
		$id = $this->id;
		$sql = "SELECT id FROM ". $this->tableName ." WHERE id = $id";
		$fields ='';
		$res = $this->findAll($sql,DB_FETCHMODE_ASSOC);
		// TODO - handle in react
		//if(empty($postArray['projectName']))  $postArray['projectName'] = 'Untitled Project';
		if($res) { //update
				$this->update();
				die(json_encode($postArray));
		} else { //create //TODO- we may not requiring create project now we can remove it after somecheck
		    foreach ($postArray as $value) {
		        $fields .= "'".$value."',";
		    }
		    $fields = rtrim($fields, ',');
				
				//TODO get fields

		    $sql = "INSERT INTO $this->tableName (id,projectName,userId) VALUES ($fields)";
		    $result =  $db->query($sql);
		}
		//return response to textBox props
	}
		
}
	

?>