<?php

class TaskController extends LMVC_Controller{

	public function init()
	{ 
		$this->setTitle('Task');

	}

	/*public function addAction(){
		LMVC_Front::getInstance()->disableLayout(true);
	}*/

	public function indexAction() {
		LMVC_Front::getInstance()->disableLayout(true); 
	  $front = LMVC_Front::getInstance();
	        
	}
	
	public function listAction(){
	    LMVC_Front::getInstance()->disableLayout(true);
			$task = new Models_Task();
			//get userid
			$params = $this->getRequest()->geturiParts(); 
			if(isset($params[2]) && is_numeric($params[2])) {
				$result = $task->getTasks($params[2]);
				$result = json_encode($result);
				print_r($result);
			} else {
				die("Invalid userId");
			}

	}
	public function updateAction() {
		LMVC_Front::getInstance()->disableLayout(true);

		if(isset($_POST["id"])) 	$id = $_POST['id'];
		$task = new Models_Task($id);

		if($this->isPost()) {
		    $task->getPostData();
		    if($id=='NEW') {
					$id = $task->create();
					$newTask = $_POST;
					$newTask['id'] = $id;
					die(json_encode($newTask));
				} else {
					$task->updateTask($_POST);
				}
		   
			}
		}

}

?>