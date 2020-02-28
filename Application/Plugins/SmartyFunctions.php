<?php

class Plugins_SmartyFunctions{

	private $exemptedProperties = array();

	public function preDispatch(LMVC_Request $request){		
		
		$smarty = LMVC_View::getInstance()->getViewRenderer()->getRenderer();
		
		$smarty->register_function("isCNSQuote", array($this,'isCNSQuote'));
		$smarty->register_function("isCNPQuote", array($this,'isCNPQuote'));
		$smarty->register_function("isCNRQuote", array($this,'isCNRQuote'));
		$smarty->register_function("isCNSPQuote", array($this,'isCNSPQuote'));
		$smarty->register_function("printSystemNotificationTypes", array($this,'printSystemNotificationTypes'));
		
		
		
				
		LMVC_Front::getInstance()->setPreDispatchVar('_plapp_vars',json_encode(array(
		    
		    'moduleName' => $request->getModuleName(),
		    'controllerName' => $request->getControllerName(),
		    'actionName' => $request->getActionName()
		    
		)));
		
		
	}
	
	public function isCNSQuote($params){	
		
		return isCNSQuote($params['quote']);
	}
	
	public function isCNPQuote($params){
		
		return isCNPQuote($params['quote']);
	}
	
	public function isCNRQuote($params){
	
		return isCNRQuote($params['quote']);
	}
	
	public function isCNSPQuote($params){
	
		return isCNSPQuote($params['quote']);
	}
	
	public function printSystemNotificationTypes(){
	    
	    $sysNotificationTypes = array(
                            	        "debug"         => PLSysNotifications::DEBUG,
                            	        "info"          => PLSysNotifications::INFO,
                            	        "notice"        => PLSysNotifications::NOTICE,
                            	        "warning"       => PLSysNotifications::WARNING,
                            	        "error"         => PLSysNotifications::ERROR,
                            	        "critical"      => PLSysNotifications::CRITICAL,
                            	        "alert"         => PLSysNotifications::ALERT,
                            	        "emergency"     => PLSysNotifications::EMERGENCY
                            	    );
	    
	    return json_encode($sysNotificationTypes);
	}
}

?>