<?php

class TaskmanagementController extends LMVC_Controller{

	public function init()
	{ 
		$this->setTitle('Task Management');

	}

	public function addAction(){
		LMVC_Front::getInstance()->disableLayout(true);
	}

	public function projectAction(){
		LMVC_Front::getInstance()->disableLayout(true);
	}

	public function indexAction() {
		LMVC_Front::getInstance()->disableLayout(true); 
	  $front = LMVC_Front::getInstance();
	        
	}
	
	public function listAction(){
	    LMVC_Front::getInstance()->disableLayout(true);
			$task = new Models_TaskManagement();
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


	public function updatetaskAction() {
		LMVC_Front::getInstance()->disableLayout(true);

		if(isset($_POST["id"])) 	$id = $_POST['id'];
		$task = new Models_TaskManagement($id);

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
		/*public function projectlistAction(){
			LMVC_Front::getInstance()->disableLayout(true);
			$project = new Models_Project();
			//get userid
			$params = $this->getRequest()->geturiParts(); 
	
			if(isset($params[2]) && is_numeric($params[2])) {
				$result = $project->getProjects($params[2]);
				$result = json_encode($result);
				print_r($result);
			} else {
				die("Invalid userId");
			}
		}*/

		public function projecttasksAction() {
			LMVC_Front::getInstance()->disableLayout(true);
			$taskmanagement = new Models_TaskManagement();
			//get userid
			$params = $this->getRequest()->geturiParts();
			if(isset($params[2]) && is_numeric($params[2])) {
				$result = $taskmanagement->getProjectsAndTasks($params[2]);
				$result = json_encode($result);
				print_r($result);
			} else {
				die("Invalid userId");
			}
		}

		public function deleteprojectAction() {
			LMVC_Front::getInstance()->disableLayout(true);
			(isset($_POST["id"])) ?	$id = $_POST['id'] : die('No projectId received to delete!');
			$project = new Models_Project($id);
			if($this->isPost()) {
				$project->deleteProject($id);
			}

		}
	
		public function updateprojectAction() {
			LMVC_Front::getInstance()->disableLayout(true);
	
			(isset($_POST["id"])) ?	$id = $_POST['id'] : die('Invalid Project ID!');
			$project = new Models_Project($id);
			
			if($this->isPost()) {
					$project->getPostData();
					if($id=='NEW') {
						$id = $project->create();
						$newProject = $_POST;
						$newProject['id'] = $id;
						die(json_encode($newProject));
					} else {
						$project->updateProject($_POST);
					}
				 
				}
			}


}

?>