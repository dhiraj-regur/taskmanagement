<?php

/**
 * Description of Request
 *
 * @author Penuel
 * @link http://www.gen-x-design.com/archives/create-a-rest-api-with-php/
 */
class LMVC_Libs_Rest_Request {

    //put your code here
    private $request_vars;
    private $data;
    private $http_accept;
    private $method;

    public function __construct() {
        $this->request_vars = array();
        $this->data = '';
        $this->http_accept = (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'xml')) ? 'xml' : 'json';
        $this->method = 'get';
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function setMethod($method) {
        $this->method = $method;
    }

    public function setRequestVars($request_vars) {
        $this->request_vars = $request_vars;
    }

    public function getData() {
        return $this->data;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getHttpAccept() {
        return $this->http_accept;
    }

    public function getRequestVars() {
        return $this->request_vars;
    }

}

?>