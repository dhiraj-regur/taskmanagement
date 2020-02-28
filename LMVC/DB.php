<?php
$pathToDB = dirname(__FILE__);		
set_include_path(get_include_path() .PATH_SEPARATOR . $pathToDB . '/Libs/PEAR');

require_once('PEAR.php');
require_once('Libs/PEAR/DB.php');

final class LMVC_DB
{	
	private static $dbObj;
	private static $instance;
	
	
	private function __construct()
	{

	}

	public static function getInstance(){
		if (!isset(self::$instance)) {
           $c = __CLASS__;
           self::$instance = new $c;
       }
		return self::$instance;
	}
	
	public function __clone() {
       trigger_error('Clone is not allowed.', E_USER_ERROR);
   	}
   	
   	public function getDB()
   	{
   		return self::$dbObj;
   	}
	
   	
    public function connect($host, $username, $password, $dbname)
    {
    	$dsn = 'mysqli://'.$username.':'.$password.'@'.$host.'/'.$dbname;
    	$options = array(
		    'debug'       => 2,
		    'portability' => DB_PORTABILITY_ALL ^ DB_PORTABILITY_LOWERCASE,
		);
    	self::$dbObj = DB::connect($dsn, $options);
    	
    	if(PEAR::isError(self::$dbObj)){
    		//throw  new LMVC_Exception(self::$dbObj->getUserInfo());
    		$this->handleError(self::$dbObj); 		
    	}
    	else{
    		self::$dbObj->setErrorHandling(PEAR_ERROR_CALLBACK, array($this,'handleError'));	
    	}
		
		return $this->getDB();    	
    }
    
    public function handleError($error_object)
    {
    	
    	if(PEAR::isError($error_object)){
				
			$back_trace = $error_object->getBackTrace();
				
			$strTrace = "";
			$back_trace= array_reverse($back_trace);

			foreach($back_trace as $trace){
				if(isset($trace['line'])){
					$strTrace .= "LINE: ". $trace['line'] ;
				}

				if(isset($trace['file'])){
					$strTrace .= " File: ". $trace['file'] ;
				}
				if(isset($trace['class'])){
					$strTrace .= " Class: ". $trace['class'] ;
				}
				if(isset($trace['function'])){
					$strTrace .= " Function: ". $trace['function'] ;
				}
				$strTrace .= "<br>" ;
			}
				
			$message = "<p align=\"left\">". $error_object->getUserInfo() ."</p>";
			if(!PEAR::isError(self::$dbObj)){
				$message .= "<p align=\"left\">LAST QUERY: <br>". self::$dbObj->last_query."</p>";	
			}
			
			$message .= "<p align=\"left\">". $strTrace ."</p>";
			if(!PEAR::isError(self::$dbObj)){
				throw new LMVC_Exception($error_object->getUserInfo());
			}
			else
			{
				echo "<pre>";
			
				echo $message;
				
				//@@todo:
				
				//mail error to webmaster
    	
    			echo "<pre>";
    				
    			die();
			}
				
			
		}
    	
    }
	
}
?>