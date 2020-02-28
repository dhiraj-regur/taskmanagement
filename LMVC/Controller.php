<?php
abstract class LMVC_Controller
{	
	private $_view;	
	private $pageTitleKey = "page_title";	
	private $_actionErrors;
	public $breadcrumb = array();
	
	final public function __construct(){
		$this->_view = LMVC_View::getInstance();
		$this->_actionErrors = array();
	}
	
	final public function setTitle($_title)
	{
		$this->_view->setViewVar($this->pageTitleKey, $_title);
	}
	final public function setNoRenderer($_noRenderer)
	{
		LMVC_Front::getInstance()->setNoRenderer($_noRenderer);
	}
	
	final public function getTitle()
	{			
		$this->_view->getViewVar($this->pageTitleKey);
	}	
	final public function  isPost()
	{
		return LMVC_Request::getInstance()->isPost();
	}
	final public function getRequest()
	{
		return LMVC_Request::getInstance();
	}	
	final public function setViewVar($_varName, $_val)
	{			
		$this->_view->setViewVar($_varName, $_val);
	}
	final public function setViewVars($_arrVars)
	{
		foreach($_arrVars as $key=> $value)
		{
			$this->_view->setViewVar($key,$value);
		}
	}
	final public function registerStylesheet($stylesheet)
	{
		$this->_view->registerStylesheet($stylesheet);		
	}		
	final public function registerHeaderScript($script)
	{
		$this->_view->registerHeaderScript($script);		
	}	
	final public function registerFooterScript($script)
	{
		$this->_view->registerFooterScript($script);
	}	
	final public function addError($err_msg)
	{
		if(is_array($err_msg))
		{
			foreach($err_msg as $msg)
			{
				array_push($this->_actionErrors, $msg);
			}
				
		}
		else
		{
			array_push($this->_actionErrors,$err_msg);
		}
		
	}	
	final public function hasErrors()
	{
		if(count($this->_actionErrors)>0)
		{
			return true;
		}
		else
		{
			return false;
		}	
	}	
	final public function getErrors()
	{
		return $this->_actionErrors;	
	}
        
      
	
	
}
?>