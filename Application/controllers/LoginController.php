<?php

class LoginController extends LMVC_Controller{

	

	public function init()
	{
		 $this->setTitle('Login');
		 
		 $layout = LMVC_Layout::getInstance();
		 
		 $formLayoutDir = $layout->getLayoutDir('Default');
	}

	public function indexAction()
	{		
		LMVC_Front::getInstance()->disableLayout(true);

		$email		= '';
		$password 	= '';
		$error_list = array();
		$error_message = "";
    $retUrl = "";
    	
    	global $siteConfig;
    	
    //	$this->registerStylesheet('/front/css/custom.css');
    	
		if($this->isPost())
		{

			$invalid_login = true;

			$email = $this->getRequest()->getPostVar('email');

			$password = $this->getRequest()->getPostVar('password');

			$remember = $this->getRequest()->getPostVar('remember');


			$retUrl = $this->getRequest()->getPostVar('retURL');

			if(trim($email)=="")
				array_push($error_list,'Please enter your email');

			if(trim($password)=="")
				array_push($error_list,'Please enter your password');


			if(empty($error_list))
			{
				$login = new Models_Taskuser();
   			$login->fetchByProperty('email',$email);
   				
				if(!$login->isEmpty)
				{	
					if($login->password == md5($password))
					{
						$invalid_login = false;
					}
					
				}

				if(!$invalid_login)
				{
					$user = new Models_Taskuser();
					$user->fetchByProperty('id',$login->id);
					LMVC_Session::set('userId',$user->id);

					if($remember == 'on') {
						setcookie("user_email", $email, time() + (86400 * 30), "/");
						setcookie("user_password", $password, time() + (86400 * 30), "/");
					} else {
						setcookie("user_email", "");
						setcookie("user_password", "");
					}
					
					//if remember set cookie email and password
					//if no remember then destroy cookie setcookie
					//use that issetCokkie in login form email value and password value
					//in login form if cookie is set checkbox must shown already checked
					
					
					if($retUrl == "")	{
						header("Location: /app");
					} else {
						header("Location: ". $retUrl);
					}
					exit();

				}
				else
				{
					$error_message = "Incorrect email or password";
				}
			}
		}
		else
		{
			$retUrl = $this->getRequest()->getVar('retURL');
		}

		if (!empty($_COOKIE['user_email']) && !empty($_COOKIE['user_password'])) {
			$this->setViewVar('email', $_COOKIE['user_email']);
			$this->setViewVar('user_password', $_COOKIE['user_password']);
		} else {
			$this->setViewVar('email', $email);
		}
		$this->setViewVar('retURL',$retUrl);
		//$this->setViewVar('password', $password);
		$this->setViewVar('error_message',$error_message);
		$this->setViewVar('error_list',$error_list);

	}

	

	

	

	

}

?>