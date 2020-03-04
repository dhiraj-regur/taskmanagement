<?php

class AppController extends LMVC_Controller {

    public function init() {
        $this->setTitle('Task Management');
    }

    public function indexAction() {
		LMVC_Front::getInstance()->disableLayout(true); 
        $front = LMVC_Front::getInstance();
        $userId = LMVC_Session::get('userId');
        if(empty($userId)) {
			//$this->setVars();
			$retUrl = $_SERVER['REQUEST_URI'];					
			header("Location: /login?retURL=$retUrl");
			exit();	
		}
        $userinfo = $this->getUserInfo($userId);
        $name = $userinfo['name'];
        $email = $userinfo['email'];
        $this->setViewVar("userId", $userId); 
        $this->setViewVar("name", $name);   
    }

    public function getUserInfo($userId) {
        $taskuser = new Models_Taskuser($userId);
        $userinfo = array(
                        'name' => $taskuser->name,
                        'email' => $taskuser->email,
                    );
        return $userinfo;
    }
    
    public function allprojectstasksAction() {
        LMVC_Front::getInstance()->disableLayout(true);
        $taskmanagement = new Models_Task();
        //get session userid
        $userId = LMVC_Session::get('userId');
        
        //also check if user exist in db
        $taskuser = new Models_Taskuser();
        $taskuser->fetchByProperty('id',$userId);
        if($taskuser->isEmpty) { //'user is deleted';
            LMVC_Session::destroy();
            header("Location: /logout/success");
            exit();
        }
       
        if(empty($userId)) {
			//$this->setVars();
			$retUrl = $_SERVER['REQUEST_URI'];
			header("Location: login?retURL=$retUrl");
			exit();	
		} else if(isset($userId) && is_numeric($userId)) {
            $result = $taskmanagement->getProjectsAndTasks($userId);
            $result = json_encode($result);
            print_r($result);
        } else {
            die("Invalid userId");
        }
    }

    public function updateprojectAction() {
        LMVC_Front::getInstance()->disableLayout(true);

        (isset($_POST["id"])) ?	$id = $_POST['id'] : die('Invalid Project ID!');
        $project = new Models_Project($id);
        $userId = LMVC_Session::get('userId');
        if(empty($userId)) {
            //$this->setVars();
            $retUrl = $_SERVER['REQUEST_URI'];
            header("Location: login?retURL=$retUrl");
            exit();	
        } else {
            if($this->isPost()) {
                $project->getPostData();
                $project->userId = $userId;
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

    public function updatetaskAction() {
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

    public function deleteprojectAction() {
        LMVC_Front::getInstance()->disableLayout(true);
        (isset($_POST["id"])) ?	$id = $_POST['id'] : die('No projectId received to delete!');
        $project = new Models_Project($id);
        if($this->isPost()) {
            $project->deleteProject($id);
        }

    }

    public function getprojecttasksAction() {
        LMVC_Front::getInstance()->disableLayout(true);
        $tasks = new Models_Task();
        $params = $this->getRequest()->geturiParts();
        if(isset($params[2]) && is_numeric($params[2])) {
            $result = $tasks->getTaskCount($params[2]);
            $result = json_encode($result[0]);
            die($result);
        } else {
            die("Invalid userId");
        }

    }







}

?>