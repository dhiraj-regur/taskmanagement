<?php
class Admin_LogoutController extends LMVC_Controller{
	
	public function init()
	{
		//$this->setTitle('Client Area');
				
	}
	
	public function indexAction()
	{		
		LMVC_Session::destroy();
		header("Location: /admin/logout/success");
		die();
	}
	
	public function successAction()
	{
		
	}
	
	
	
	
}
?>