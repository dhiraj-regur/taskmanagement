<?php
require('Loader.php');
class LMVC_Front {
	private $applicationDir;
	private $directories;
	private $requestURI;
	private $isCLIMode = false;
	
	private $_plugins;
	private $_viewRenderer;
	private static $instance;
	private static $_controllerObj;
	private static $_viewObj;
	private static $_loaderObj;
	//private static $_layoutObj;
	private static $_requestObj;
	private $_predispatchVars = array();
	private $_postdispatchVars;
	private $noRenderer = false;
	private $layoutEnabled = true;
	private static $_exceptionObj = null;


	private function __construct(){
		$pathToFront = dirname(__FILE__);
		set_include_path(get_include_path() .PATH_SEPARATOR . $pathToFront);
			
		$this->directories 	= array();
		self::$_loaderObj	= new Loader();
		//self::$_layoutObj	= LMVC_Layout::getInstance();
		self::$_requestObj	= LMVC_Request::getInstance();
		$this->_plugins		= new LMVC_PluginBroker();
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


	public function setApplicationDirectory($_appDir){
		$this->applicationDir	= str_replace('\\', '/',$_appDir);
		$this->applicationDir = $this->removeTrailingSlashes($this->applicationDir);
	}

	public function getApplicationDirectory(){
		return $this->applicationDir;
	}

	public function setControllerDirectory($_directories){
		$this->directories = $_directories;
	}
	
	public function enableCLI()
	{
		$this->isCLIMode = true;
		$this->disableLayout(true);
		$this->setNoRenderer(true);
	}

	public function setNoRenderer($_noRenderer)
	{
		$this->noRenderer = $_noRenderer;
	}
	public function disableLayout($_disable)
	{
		$this->layoutEnabled = (true == $_disable)?false:true;
	}

	public function isLayoutEnabled()
	{
		return $this->layoutEnabled;
	}

	public static function setLayout($module_name, $layout_dir, $layout_file=null){
		LMVC_Layout::getInstance()->setLayout($module_name, $layout_dir, $layout_file);
	}

	public static function getLayoutObject(){
		return LMVC_Layout::getInstance();
	}

	public static function getException()
	{
		return self::$_exceptionObj;
	}
	public static function getControllerObject(){
		return  self::$_controllerObj;
	}

	public static function getViewObject(){
		return  self::$_viewObj;
	}

	public function registerPlugin($_plugin, $stack_index=null){
		$this->_plugins->registerPlugin($_plugin, $stack_index);
	}

	public function setPreDispatchVar($_varName,$_value)
	{
		$this->_predispatchVars[$_varName] = $_value;

	}
	public function getPreDispatchVars($_varname = "")
	{
		if($_varname=="")
		{
			return $this->_predispatchVars;
		}
		else
		{
			if(isset($this->_predispatchVars[$_varname]))
			{
				return $this->_predispatchVars[$_varname];
			}
		}

	}


	public function dispatch($_moduleName = "", $_controllerName = "", $_actionName =""){

		if(!$this->isCLIMode)
		{
			self::$_requestObj->setupRequest(array_keys($this->directories));
		}
		
		//get module
		$moduleName = (empty($_moduleName))?self::$_requestObj->getModuleName():$_moduleName;

		//get controller
		$controllerName =  (empty($_controllerName))?self::$_requestObj->getControllerName():$_controllerName;

		//get action
		$actionName = (empty($_actionName))?self::$_requestObj->getActionName():$_actionName;

		//params
		$params =  self::$_requestObj->getParams();
			

		$controllerDir = "/controllers";	//default controller dir
		if(array_key_exists($moduleName, $this->directories)){
			$controllerDir = "/". $this->removeEndSlashes($this->directories[$moduleName]);
		}
		$controllerDir = $this->applicationDir . $controllerDir; //prepend root application dir

		$pathInfo = array('module'=>$moduleName, 'controller'=>$controllerName,'controllerDir'=> $controllerDir, 'action'=>$actionName,'params'=>$params);

		#run the registered plguins
		$this->_plugins->preDispatch(self::$_requestObj);


		//Controller Class Name
		$controllerClassName =	"";
		$bareControllerClassName =  ucfirst($controllerName) ."Controller";
		if($moduleName != "Default"){
			$controllerClassName =  ucfirst($moduleName) ."_";
		}
		$controllerClassName .= $bareControllerClassName;

		//include the controller file
		$controllerFile = $controllerDir;
		$controllerFile .= "/".  ucfirst($controllerName) ."Controller.php";	#controller file

		try
		{

			if(file_exists($controllerFile)){
				require($controllerFile);
					
				if(class_exists($controllerClassName,FALSE)){
					$controllerObj = new $controllerClassName;	#controller object
					self::$_controllerObj = $controllerObj;
					$_actionName = $actionName ."Action";	#action

					if(!is_subclass_of($controllerObj,'LMVC_Controller'))
					{
						trigger_error('Controller must be of type LMVC_Controller', E_USER_ERROR);
					}
				//	echo "<pre>";print_r($controllerObj); die('<=frontdead');
					if(method_exists($controllerObj, $_actionName) && is_callable(array($controllerObj, $_actionName))){
							
						if(method_exists($controllerObj,'init')){

							call_user_func(array($controllerObj,'init'));	#call action

						}

						#init View object.

						$viewObj = LMVC_View::getInstance();
						$layoutObj = LMVC_Layout::getInstance();

						self::$_viewObj = $viewObj;
						call_user_func(array($controllerObj,$_actionName));	#call action


						if(false == $this->noRenderer)
						{

							$layoutDir	= $this->applicationDir . $layoutObj->getLayoutDir($moduleName);
							$layout		=$layoutObj->getLayoutFile($moduleName);
								

							if($viewObj->getViewDir()=="") #detect view directory based on current module,controller and action
							{
								$viewDir = $this->applicationDir;
								if($moduleName !="Default")
								{
									$viewDir .= "/". $moduleName;
								}


								#setup view directories and files
								$viewDir .= "/views/scripts/". $controllerName;
							}
							else
							{
								$viewDir = $viewObj->getViewDir();
							}

							if($viewObj->getViewFile()=="")	{
								$viewFile = $actionName .".html"; #default to current action name
							}
							else
							{
								$viewFile = $viewObj->getViewFile() .".html"; #useful if you want to render another view for current action
							}



							if(false == $this->layoutEnabled || file_exists($layoutDir ."/". $layout))
							{



								if($this->isLayoutEnabled())
								{
									$viewObj->setLayoutDir($layoutDir);
									$viewObj->setLayout($layout);
								}

								if(file_exists($viewDir . "/". $viewFile))
								{
									$viewObj->setViewDir($viewDir);
									$viewObj->setViewFile($viewFile);
									$viewObj->setViewVar('path_info',$pathInfo);
									$viewObj->setViewVars($this->getPreDispatchVars());		#set the var values from the pre-dispatch plugins
									$viewObj->render();
								}
								else
								{
									trigger_error("Required View file ($viewFile) not found at $viewDir", E_USER_ERROR);
								}
							}
							else{
								trigger_error("Required Layout file ($layout) not found at $layoutDir", E_USER_ERROR);
							}
						}
					}
					else{ //return;
						trigger_error("Required Action ($actionName) not found in controller class $controllerClassName", E_USER_ERROR);
					}
				}
				else{
					trigger_error("Unable to load class: $controllerClassName", E_USER_ERROR);
				}
			}
			else{
				trigger_error("Required Controller ($bareControllerClassName) not found at $controllerDir", E_USER_ERROR);
			}
		}
		catch(LMVC_Exception $e)
		{

			 

			$err_info = "";
			$err_info.= "An exception has occured:\n";
			$err_info.= "File: " . $e->getFile() . "\n";
			$err_info.= "Line: " . $e->getLine() . "\n";
			$err_info.= "Message: \n" . $e->getMessage() . "\n\n";
			$err_info.= "Back Trace: \n" . $e->getTraceAsString() . "\n";
			$err_info.= "Module: ". LMVC_Request::getInstance()->getModuleName() . "\n\n";
			$err_info.= "Controller: " . LMVC_Request::getInstance()->getControllerName() . "\n";
			$err_info.= "Action: " . LMVC_Request::getInstance()->getActionName() . "\n";
			$err_info.= "Params: " . print_r(LMVC_Request::getInstance()->getParams(),true) . "\n";
			$err_info.= "GET: " . print_r(LMVC_Request::getInstance()->getGet(),true) . "\n";
			$err_info.= "POST: " . print_r(LMVC_Request::getInstance()->getPost(),true) . "\n";
			if(!empty($_SESSION)) $err_info.= "Session: " . print_r($_SESSION,true) . "\n";
			$e->dump = $err_info;
			self::$_exceptionObj = $e;			
			$this->setNoRenderer(false);	//make sure view is enabled to print the exception on screen.

			//check for controllers.
			ob_end_clean();
			if(file_exists($controllerDir . "/ErrorController.php"))
			{
				LMVC_View::destroyInstance();
				$this->dispatch(null,'error','error');
				return;
			}
			elseif(file_exists($this->applicationDir . "/controllers/ErrorController.php"))
			{
				LMVC_View::destroyInstance();
				$this->dispatch('Default','error','error');
				return;
			}
			else
			{
				echo "<pre>";
				echo $err_info;
				echo "</pre>";
			}
			 

		}

	}



	private function removeEndSlashes($_path){
		$path = $_path;
		if($path!=""){
			if(substr($path,0,1)=="/"){
				$path = substr($path,1,strlen($path));
			}
			if(substr($path,strlen($path)-1,1)=="/"){
				$path = substr($path,0,strlen($path)-1);
			}
		}
		return $path;
	}

	private function removeTrailingSlashes($_path){
		$path = $_path;
		if($path!=""){
			if(substr($path,strlen($path)-1,1)=="/"){
				$path = substr($path,0,strlen($path)-1);
			}
		}
		return $path;
	}
	
	public function isCliMode(){
	    return $this->isCLIMode;
	}
}
?>