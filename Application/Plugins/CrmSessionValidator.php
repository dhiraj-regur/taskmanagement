<?php

class Plugins_CrmSessionValidator{

	

	private $exemptedProperties = array();

	

	public function preDispatch(LMVC_Request $request)

	{

		$this->exemptedProperties['login'] = array('index');

		$this->exemptedProperties['logout'] = array('index','success');
	

		if($request->getModuleName() == "crm")
		{

			$this->setVars();

			LMVC_View::getInstance()->registerHeaderScript('/js/angular.min.js');
			LMVC_View::getInstance()->registerHeaderScript('//ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular-sanitize.js');
			LMVC_View::getInstance()->registerHeaderScript('/assets/js/ngapp-config.js');

			$currentController = $request->getControllerName();

			$currentAction = $request->getActionName();

			

			if(array_key_exists($currentController,$this->exemptedProperties))

			{

				

				if(in_array($currentAction,$this->exemptedProperties[$currentController]))

				{

					return;

				}

				else{

					

					$this->validateSession();

					

				}

				

			}

			else

			{

				$this->validateSession();

			}

			

			

		}



	}

	

	private function validateSession()
	{

		$id = LMVC_Session::get('crmUserId');
		if(empty($id))
		{
			$this->setVars();
			$retUrl = $_SERVER['REQUEST_URI'];
			header("Location: /crm/login?retURL=$retUrl");
			exit();	

		}
		else
		{
			$this->checkAuthorization();
		}	

	}

	private function checkAuthorization()
	{
		$acl = Service_ACL::getInstance();
		if(!$acl->currentUserHasAccess())
		{
			
			header("Location: /crm/unauthorized/");
			exit();
		}
		else
		{
			
		}
	}

	private function setVars()
	{

		$crmUserId = LMVC_Session::get('crmUserId');		
		$crmUserName = LMVC_Session::get('crmUserName');
		$crmUserRole = LMVC_Session::get('userRole');
		
		if(empty($crmUserId)) $crmUserId=0;

		LMVC_Front::getInstance()->setPreDispatchVar('session_crm_user_id',$crmUserId);
		LMVC_Front::getInstance()->setPreDispatchVar('session_crm_user_name',$crmUserName);
		LMVC_Front::getInstance()->setPreDispatchVar('session_crm_user_role',$crmUserRole);
		LMVC_Front::getInstance()->setPreDispatchVar('acl',  Service_ACL::getInstance());
	}

	

	

}

?>