<?php

class LogoutController extends LMVC_Controller{

	

	public function init()

	{

		$this->setTitle('Logout');

		$layout = LMVC_Layout::getInstance();
		
		$formLayoutDir = $layout->getLayoutDir('Default');
	

	}

	

	public function indexAction()

	{		

		LMVC_Session::destroy();
		
	//	$this->registerStylesheet('/front/css/custom.css');

		header("Location: /logout/success");

		die();

	}

	

	public function successAction()

	{

		$this->registerStylesheet('/front/css/custom.css');

	}

	

	

	

	

}

?>