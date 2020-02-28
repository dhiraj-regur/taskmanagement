<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Route
 *
 * @author Penuel
 */
class LMVC_Router {

    //put your code here

    private static $instance;
    private $routes = array();
    private $matchedRouteId = "";
    private $matchedRoute;

    private function __construct() {
        
    }

    public static function getInstance() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }

    public function addRoute($routeId, $route) {
        $this->routes[$routeId] = $route;
    }

    public function getRoutes() {
        return $this->routes;
    }
    
    public function getMatchedRouteId()
    {
    	return $this->matchedRouteId;
    }

    public function matchRoute($uriParts = array()) {
    	
    	$this->matchedRoute = '';
    	$this->matchedRouteId = '';
    	
        foreach ($this->routes as $id => $route) {
            $pattern = $route->getRoutePattern();
            $pattern = $this->removeEndSlashes($pattern);
            $patternParts = explode("/", $pattern);

            if (count($patternParts) == count($uriParts)) {

                $matched = true;
                foreach ($patternParts as $key => $val) {
                    if (strpos($val, ":", 0) === false) { #static url part
                        if ($uriParts[$key] != $val) {
                            $matched = false;
                        }
                    }
                }
                if ($matched == true) {
                    $this->matchedRoute = $route;
                    $this->matchedRouteId = $id;
                    break;
                }
            }
        }
        if (is_object($this->matchedRoute)) {
            return $this->matchedRoute;
        } else {
            return FALSE;
        }
    }

    public function convertRoute($route, $request) {
        $handler = $route->getRouteHandler();

        if (array_key_exists('module', $handler)) {
            $request->setModuleName($handler['module']);
        }

        if (array_key_exists('controller', $handler)) {
            $request->setControllerName($handler['controller']);
        }

        if (array_key_exists('action', $handler)) {
            $request->setActionName($handler['action']);
        }

        $pattern = $route->getRoutePattern();
        $pattern = $this->removeEndSlashes($pattern);
        $patternParts = explode("/", $pattern);
        $uriParts = $request->getURIParts();
        $params = array();

        if (count($patternParts) == count($uriParts)) {
            foreach ($patternParts as $key => $val) {
                if (strpos($val, ":", 0) !== false) {
                    $params[str_replace(":", "", $val)] = $uriParts[$key];
                }
            }
        }
        $request->setParams($params);
    }

    private function removeEndSlashes($_path) {
        $path = $_path;
        if ($path != "") {
            if (substr($path, 0, 1) == "/") {
                $path = substr($path, 1, strlen($path));
            }
            if (substr($path, strlen($path) - 1, 1) == "/") {
                $path = substr($path, 0, strlen($path) - 1);
            }
        }
        return $path;
    }

}

?>
