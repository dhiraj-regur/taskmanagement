<?php
class Plugins_APIValidator{
	
	private $apiModules = array();
	
	public function addAPIModule($_moduleName)
	{
		array_push($this->apiModules, strtolower($_moduleName));
	}
	
	public function preDispatch(LMVC_Request $request)
	{		
		$currentModuleName = $request->getModuleName();
		if(in_array(strtolower($currentModuleName), $this->apiModules))
		{
			$router = LMVC_Router::getInstance();			
			$apiRouteId = $router->getMatchedRouteId();			
			
			if(empty($apiRouteId))
			{	
				ob_end_clean();	//clean any output before sending response.
				LMVC_Libs_Rest_Utils::sendResponse(404);
				
			}
			
		}
		
		
		
	}
	
		
}
?>