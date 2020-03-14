<?php

class IndexController extends LMVC_Controller{



	public function init()

	{

		$this->setTitle(' App');			

	}



	public function indexAction()

	{
		LMVC_Front::getInstance()->disableLayout(true);
		$front = LMVC_Front::getInstance();
		$userId = LMVC_Session::get('userId');
		if(empty($userId)) {
			//$this->setVars();
			$retUrl = $_SERVER['REQUEST_URI'];
			if($retUrl == "/") {
				header("Location: /login");
			}	else {
				header("Location: /login?retURL=$retUrl");
			}			
			
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

}

?>