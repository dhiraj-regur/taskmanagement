<?php
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../Application/'));
define('MVC_PATH', realpath(dirname(__FILE__) . '/../'));
require('ApplicationConfig.php');

require('LMVC/Front.php');


require(APPLICATION_PATH . "/Bootstrap.php");

?>