<?php

class RegisterController extends LMVC_Controller {

    public function init() {
        $this->setTitle('Task Management');
    }

    public function indexAction() {
      LMVC_Front::getInstance()->disableLayout(true); 
      $front = LMVC_Front::getInstance();

      $taskuser = new Models_Taskuser();
      if($this->isPost()) {

        $taskuser->getPostData();
        $userId = 0;
        //check if email already exist
        $taskuser->fetchByProperty('email', $taskuser->email);
        if(!$taskuser->isEmpty) {
          $this->addError("This email already exist.");
        } else {
          $taskuser->password = md5($taskuser->password);
          $userId = $taskuser->create();
        }

        if($userId>0) {
            //set session
            LMVC_Session::set('userId',$userId);
            $userId = LMVC_Session::get('userId');
            //create one default project
            $project = new Models_Project();
            $project->userId = $userId;
            $project->projectName = 'Untitled Project';
            $project->create();
            header("Location:/app");
        } else {
            $this->setViewVar('error_list', $this->getErrors());
        }

      }
    }

}



?>