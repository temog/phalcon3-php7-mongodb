<?php

define('APP_PATH', realpath('..'));

try{

	$config = include APP_PATH . "/app/config/config.php";

	include APP_PATH . "/app/config/loader.php";
	include APP_PATH . "/app/config/services.php";

	$application = new \Phalcon\Mvc\Application($di);

	if($di->get('config')->env == 'production'){
		ini_set('display_errors', 'Off');
	}
	else {
		ini_set('display_errors', 'On');
	}

	echo $application->handle()->getContent();

}
catch (\Exception $e){
	echo $e->getMessage();
}

