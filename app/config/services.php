<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Session\Adapter\Files as SessionAdapter;

require_once($config->application->vendorDir . 'autoload.php');

$di = new FactoryDefault();

$di->set('config', function() use ($config){
	return $config;
});

$di->set('url', function () use ($config) {
	$url = new UrlResolver();
	$url->setBaseUri($config->application->baseUri);

	return $url;
});

$di->set('baseUrl', function() use ($config){
	$baseUrl = new Phalcon\Mvc\Url();
	$base = isset($_SERVER['HTTPS_HOST'])?
		'https://' . $_SERVER['HTTPS_HOST'] . $config->application->baseUri:
		'http://' . $_SERVER['HTTP_HOST'] . $config->application->baseUri;
	$baseUrl->setBaseUri($base);
	return $baseUrl;
});

$di->setShared('view', function () use ($config) {

	$view = new View();

	$view->setViewsDir($config->application->viewsDir);

	$view->registerEngines(array(
		'.volt' => function ($view, $di) use ($config) {

			$volt = new VoltEngine($view, $di);
			$volt->setOptions(array(
				'compiledPath' => $config->application->cacheDir,
				'compiledSeparator' => '_',
				'stat' => true,
				'compileAlways' => true,
			));

			return $volt;
		},
		'.phtml' => 'Phalcon\Mvc\View\Engine\Php',
	));

	return $view;
});


foreach($config->database->{$config->env} as $dbKey => $db){

	$di->set($dbKey, function () use ($db) {

		$server = "mongodb://";

		$query = [];
		if($db->username){
			$server .= $db->username . ':' . $db->password . '@';
			$query['authsource'] = $db->dbname;
		}

		if($db->replicaSet){
			$query['replicaSet'] = $db->replicaSet;
		}

		$server .= $db->host;

		if($query){
			$server .= '/?' . http_build_query($query);
		}

		if($db->uriOptions){
			return new MongoDB\Client($server, (array) $db->uriOptions);
		}

		return new MongoDB\Client($server);
	});
}


$di->setShared('session', function () use ($config) {

    $session = new SessionAdapter();

	$configSess = $config->session->{$config->env};
	session_name($configSess->name);
	session_save_path($configSess->sessionDir);
	session_set_cookie_params(
		$configSess->lifetime,
		$configSess->path,
		$configSess->domain,
		$configSess->secure
	);

    $session->start();

    return $session;
});

$di->set('flashSession', function(){

	$flash = new \Phalcon\Flash\Session(array(
		'error' => 'uk-alert-danger uk-alert',
		'success' => 'uk-alert-success uk-alert',
		'notice' => 'uk-alert-primary uk-alert',
	));
	return $flash;
});

$di->set('helper', function(){
	return new Helper();
});

$di->set('security', function(){

	$security = new Phalcon\Security();

	$security->setWorkFactor(12);
	return $security;
});

