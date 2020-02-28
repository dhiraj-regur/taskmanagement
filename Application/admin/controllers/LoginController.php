<?php
class Admin_LoginController extends LMVC_Controller{
	
	public function init()
	{
		$this->setTitle('Login');
				
	}
	
	public function indexAction()
	{		
		$username		= '';
		$password 	= '';
		$error_list = array();
		$error_message = "";
		$retUrl = "";
		
		
		if($this->isPost()){
			$invalid_login = true;
			
			
			$username = $this->getRequest()->getPostVar('username');
			$password = $this->getRequest()->getPostVar('password');
			
			$retUrl = $this->getRequest()->getPostVar('retURL');
			
			
			if(trim($username)=="")
				array_push($error_list,'Please enter your username');
				
			if(trim($password)=="")
				array_push($error_list,'Please enter your password');

			
			if(empty($error_list))
			{
				$user = new Models_User();
				$user->fetchByProperty('username',$username);
				if(!$user->isEmpty){
					if($user->active == 'y'  && $user->password == md5($password))
					{
						$invalid_login = false;
					}								
				}
				
				if(!$invalid_login)
				{
					
					LMVC_Session::set('adminId',$user->id);
					LMVC_Session::set('adminUserName',$user->username);
					LMVC_Session::set('userRole',$user->role);
					
					if($retUrl == "")					
						header("Location: /admin/index");
					else
						header("Location: ". $retUrl);
					exit();
					
				}
				else
				{
					$error_message = "Incorrect username or password";
					array_push($error_list, $error_message);
				}
			}
			
			
		}
		else
		{
			$retUrl = $this->getRequest()->getVar('retURL');
		}
		
		
		$this->setViewVar('retURL',$retUrl);
		$this->setViewVar('username', $username);
		$this->setViewVar('password', $password);
		//$this->setViewVar('error_message',$error_message);
		$this->setViewVar('error_list',$error_list);
		
		
		
		
		
		
		
	}
	
	
	
	
}
?>