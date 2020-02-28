<?php
class Plugins_AdminSessionValidator{
	
	private $exemptedProperties = array();
             
	
	public function preDispatch(LMVC_Request $request)
	{
		$this->exemptedProperties['login'] = array('index');
		$this->exemptedProperties['logout'] = array('index','success');
		
		if($request->getModuleName() == "admin")
		{
			$this->setUIContext($request);
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
		$id = LMVC_Session::get('adminId');
		if(empty($id))
		{			
			$this->setVars();
			$retUrl = $_SERVER['REQUEST_URI'];			
			header("Location: /admin/login?retURL=$retUrl");
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
                
                header("Location: /admin/unauthorized/");
                exit();	
            }
            else
            {
                
            }
        }
        
      
	
	private function setVars()
	{
		$id = LMVC_Session::get('adminId');		
		$username = LMVC_Session::get('adminUserName');
		if(empty($id)) $id=0;
		LMVC_Front::getInstance()->setPreDispatchVar('session_admin_id',$id);		
		LMVC_Front::getInstance()->setPreDispatchVar('session_admin_username',$username);
        LMVC_Front::getInstance()->setPreDispatchVar('acl',  Service_ACL::getInstance());
	}
	
	
	private function setUIContext($request)
	{
		// set UI view var from query string param
		$uiContext = $request->getVar("ui_context");
		if($uiContext != "")
		{
			LMVC_Session::set('ui_context', $uiContext);
		}
		
		$sessionUIContext = LMVC_Session::get("ui_context");
		LMVC_Front::getInstance()->setPreDispatchVar('ui_context',$sessionUIContext);
	}
	
}
?>