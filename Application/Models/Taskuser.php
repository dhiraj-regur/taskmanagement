<?php

class Models_Taskuser extends LMVC_ActiveRecord {

	public $tableName = "users";
	public $id				= "";
	public $name  		= "";
	public $email			= "";
	public $password	= "";

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
	

		
}
	

?>