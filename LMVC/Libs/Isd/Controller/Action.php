<?php

abstract class Isd_Controller_Action extends Zend_Controller_Action
{
	/**
	 * Initializes the controller & context
	 * @see: http://www.web-punk.com/2010/03/zend-framework-applications-for-iphone-blackberry-co/
	 * @return void
	 */
	public function init(){
	    parent::init();
	
	   if ($this->isMobile()) {
	     	// Mobile format context
	    	$mobileConfig =
		        array(
	             'mobile' => array(
	                	'suffix'  => 'mobile',
	            	    'headers' => array(
	        	            'Content-type' => 'text/html; charset=utf-8')),
	    	    );
	
	 	    // Init the action helper
	    	$contextSwitch = $this->_helper->contextSwitch();
	
		    // Add new context
	     	$contextSwitch->setContexts($mobileConfig);
	
	    	// This is where you have to define
	    	// which actions are available in the mobile context
	    	// ADOPT THIS TO YOUR NEEDS!
	    	$contextSwitch->addActionContext('index', 'mobile');
	    	//$contextSwitch->addActionContext('akcje', 'mobile');
	
	    	// enable layout but set different path to layout file
	    	$contextSwitch->setAutoDisableLayout(false);
	    	$this->getHelper('layout')->setLayoutPath(APPLICATION_PATH . '/layouts/mobile');
	
	    	// Initializes action helper
	    	$contextSwitch->initContext('mobile');
	    	
	    }
	}
	
	public function isMobile(){
		return Zend_Registry::get('context') == '\mobile' || $this->getRequest()->getParam('format') == 'mobile';
	}
	
	
    public function forward($action, $controller = null, $module = null, array $params = null) {
        $this->_forward($action, $controller, $module, $params);
    }

    protected $_redirector = null;

	public function redirect($action = null, $controller = null, $module = null, $params = array(), $route = null, $reset = true, $encode = true) {
        //gotoSimple($action, $controller = null, $module = null, array $params = array())
        //gotoRoute(array $urlOptions = array(), $name = null, $reset = false, $encode = true)
        //gotoUrl($url, array $options = array())

        if ($this->_redirector == null)
            $this->_redirector = $this->_helper->getHelper('Redirector');

        // jesli pierwszy czlon zaczyna sie od http(s): przekieruj do gotoUrl
        if (preg_match("#^http(s)?:#i", $action)) {
            $url = $action;
            $options = ( is_array($controller) ? $controller : array() );
            return $this->_redirector->gotoUrl($url, $options);
        }


        // zmienna $params moze byc pominieta, wtedy przesun parametry o jeden w dol
        if (is_string($params)) {
            $encode = $reset;
            $reset = $route;
            $route = $params;
            $params = array();
        }

        // jesli przekierowujemy z routerem
        if (is_string($route)) {
            $params = array_merge(array('action' => $action, 'controller' => $controller, 'module' => $module), $params);
    		return $this->_redirector->gotoRoute($params, $route, $reset, $encode);
    	}

        
        // jesli parametry sa puste probuj przekierowac do biezacego lub domyslnego modulu/kontrolera/akcji
        $front = $this->getFrontController();
        if ($action == null || $action == '') {
            $action = $this->getRequest()->getActionName();
        } elseif ($action == '$') {
            $action = $front->getDefaultAction();
        }
        if ($controller == null || $controller == '') {
            $controller = $this->getRequest()->getControllerName();
        } elseif ($controller == '$') {
            $controller = $front->getDefaultControllerName();
        }
        if ($module == null || $module == '') {
            $module = $this->getRequest()->getModuleName();
        } elseif ($module == '$') {
            $module = $front->getDefaultModule();
        }

	    return $this->_redirector->gotoSimpleAndExit($action, $controller, $module, $params);
    }
}
