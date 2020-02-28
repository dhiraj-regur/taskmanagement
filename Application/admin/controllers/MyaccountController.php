<?php
class Admin_MyaccountController extends LMVC_Controller{
	
	public function init(){		
		$this->setTitle('My Account');
	}
	

	
	
	

	
	
	public function indexAction()
	{
		
		
	
		
		
		
		$userId = LMVC_Session::get("adminId");		
		$user = new Models_User($userId);		
				
		if($this->isPost())
		{
			$password = $this->getRequest()->getPostVar("password");
			$password = $this->getRequest()->getPostVar("confirmPassword");
			$ignoreFields = array();
			if($password=="")
			{
				array_push($ignoreFields,"password");
				array_push($ignoreFields,"confirmPassword");
			}
			
			$user->getPostData($ignoreFields);
			$result = $user->update();
			if($result)			
			{
								
				header("Location: /admin/myaccount/?success=user info changed");
				exit();
			}
			else
			{
				$this->addError($user->getErrors());
			}			
		}		
		
		
		if($this->hasErrors())
		{
			$this->setViewVar('error_list',$this->getErrors());	
		}
		$this->setViewVar('user',$user);	
		
		$success_msg = $this->getRequest()->getVar('success');		
		if($success_msg == "user info changed")
		{
			$this->setViewVar('success_msg','User information updated successfully');
		}		
		elseif($success_msg == "user added")
		{
			$this->setViewVar('success_msg','A new user was added successfully');
		}
	
		
	}
	
	

	
	public function deleteAction()
	{
		
		if($this->isPost())
		{
			$status = "";
			$mesage = "";
		
			$userId = $this->getRequest()->getPostVar('id');		
			$user = new Models_User($userId);
			if(!$user->isEmpty)
			{
				$user->delete();					
				$status = 1;
				$message = "User deleted successfully";
			}
			else
			{
				$status = 0;
				$message = "There was some error deleting the user";
					
			}
			
			$response = array('status'=>$status, 'message'=> $message);
			
			header('Content-type: application/json');
			echo json_encode($response);
		}
		die();
	}
	
		
}
?>