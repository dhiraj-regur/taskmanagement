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

		$this->setViewVar('retURL',$retUrl);
		$this->setViewVar('email', $email);
		$this->setViewVar('password', $password);
		$this->setViewVar('error_message',$error_message);
		$this->setViewVar('error_list',$error_list);

	}

	

	

	

	

}

?>