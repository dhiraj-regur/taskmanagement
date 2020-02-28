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
class LMVC_Route {

    //put your code here

    private $routePattern = '';
    private $routeHandler = '';

    public function __construct($route, $handler = array()) {
        $this->routePattern = $route;
        $this->routeHandler = $handler;  //handler defines controller and action;
    }

    public function getRoutePattern() {
        return $this->routePattern;
    }
    
    public function getRouteHandler() {
        return $this->routeHandler;
    }

}

?>
