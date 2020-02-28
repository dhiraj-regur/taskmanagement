<?php
class LMVC_Logger
{
  
    
    private $logFilePath = '';

    public function __construct() {
    	
    }
    
    public function setLogFilePath($_path, $createFileIfNotExists = false)
    {
    	if(true == $createFileIfNotExists)
    	{
    		$handle = fopen($_path, "a") or trigger_error('Log file could not be created',E_USER_WARNING);
    		if(FALSE !== $handle)
    		{
    			fclose($handle);
    			$this->logFilePath = $_path;
    		}    		
    	}
    	else
    	{
    		if(file_exists($_path) && is_writable($_path))
    		{
    			$this->logFilePath = $_path;
    		}
    		else
    		{
    			trigger_error('Log file not found or not writeable',E_USER_WARNING);
    		}
    	}
    	
    	
    }
    
    public function log($_log, $_removeExtraInfo=false)
    {
        if(!$_removeExtraInfo)
        {            
            $log = "=======================================================================\n";
            $log.= "DateTime: ". date('d-m-Y H:i:s') ."\n";
            $log.= "Module: ". LMVC_Request::getInstance()->getModuleName() ."\n";
            $log.= "Controller: " . LMVC_Request::getInstance()->getControllerName() . "\n";
            $log.= "Action: " . LMVC_Request::getInstance()->getActionName() . "\n";

            $front = LMVC_Front::getInstance();
            
            if($front->isCliMode() == false) {

                $log.= "Params: " . print_r(LMVC_Request::getInstance()->getParams(),true) . "\n";
                $log.= "GET: " . print_r(LMVC_Request::getInstance()->getGet(),true) . "\n";
                $log.= "POST: " . print_r(LMVC_Request::getInstance()->getPost(),true) . "\n";
                $log.= "URL:". $_SERVER['REQUEST_URI'] ."\n"; 
                
            }
           
            

            $log.= "Log: ". $_log ."\n\n";              
        }
        else
        {
            $log = $_log;
        }
    	  	
    	error_log($log,3, $this->logFilePath);
    }


}

?>