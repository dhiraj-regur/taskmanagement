<?php

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../Application/'));
define('MVC_PATH', realpath(dirname(__FILE__) . '/../'));
set_include_path(get_include_path() .PATH_SEPARATOR . MVC_PATH);
set_include_path(get_include_path() . PATH_SEPARATOR . APPLICATION_PATH);

require(MVC_PATH.'/vendor/autoload.php');

$envFactory = new Dotenv\Environment\DotenvFactory([
    new Dotenv\Environment\Adapter\EnvConstAdapter(),
    new Dotenv\Environment\Adapter\PutenvAdapter()
]);

$dotenv = Dotenv\Dotenv::create(MVC_PATH, null, $envFactory);
$dotenv->load();
$dotenv->required(['ENV','DB_HOST', 'DB_UNAME', 'DB_PWD', 'DB_NAME']);

require('ApplicationConfig.php');
$_SERVER['DOCUMENT_ROOT']= SITE_ROOT;
$_SERVER['HTTP_HOST']= getenv('HTTP_HOST');

require('LMVC/Front.php');





require(APPLICATION_PATH . "/Bootstrap-cli.php");



?>