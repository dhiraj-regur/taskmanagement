<?php
class Admin_SfrefreshtokenController extends LMVC_Controller
{

	public function init()
	{
		$this->setTitle('SF Refresh Token');

	}
	
	
	public function indexAction() {
		
		global $siteConfig;
		
		$sfRefreshToken = $siteConfig->get('sf_refresh_token');
		$this->setViewVar('refreshToken', $sfRefreshToken);
		
		
		if(TRUE === SF_PRODUCTION_MODE) {
			$sfAuthUrl = SF_LIVE_AUTH_URL;
		} else {
			$sfAuthUrl = SF_SANDBOX_AUTH_URL;
		}
		
		$this->setViewVar('sfAuthUrl', $sfAuthUrl);
		$this->setViewVar('sfClientId', SF_CLIENT_ID);
		$this->setViewVar('sfRedirectUri', SF_REDIRECT_URI);
		
		
		$status = $this->getRequest()->getVar('status');
		if($status == 'success') {
			
			$this->setViewVar('success_msg', 'Refresh token generated and updated successfully');
			
		} else if($status == 'error') {
			
			$error_msg = $this->getRequest()->getVar('message');
			$this->addError($error_msg);
			$this->setViewVar('error_list', $this->getErrors());
			
		}
		
	}
	
	
	public function generateAction() {
		
		$this->setNoRenderer(true);
		
	}
	
}