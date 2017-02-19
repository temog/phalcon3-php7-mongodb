<?php

use Phalcon\DI\FactoryDefault\CLI as CliDI,
	Phalcon\CLI\Console as ConsoleApp;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Session\Adapter\Files as SessionAdapter;

define('VERSION', '1.0.0');

$di = new CliDI();

defined('APPLICATION_PATH') ||
	define('APPLICATION_PATH', realpath(dirname(__FILE__)));

$loader = new \Phalcon\Loader();
$loader->registerDirs(
	[
		APPLICATION_PATH . '/tasks',
		APPLICATION_PATH . '/models/',
		APPLICATION_PATH . '/library/',
	]
);
$loader->register();

if(is_readable(APPLICATION_PATH . '/config/config.php')) {

	$config = include APPLICATION_PATH . '/config/config.php';
	$di->set('config', $config);

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
		session_name($config->session->name);
		session_save_path($config->session->sessionDir);
		session_set_cookie_params(
			$config->session->lifetime,
			$config->session->path,
			$config->session->domain,
			$config->session->secure
		);

		$session->start();

		return $session;
	});

	$di->set('flashSession', function(){

		$flash = new \Phalcon\Flash\Session(array(
			'error' => 'alert alert-danger',
			'success' => 'alert alert-success',
			'notice' => 'alert alert-info',
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

}

$console = new ConsoleApp();
$console->setDI($di);

$arguments = array();
$params = array();

foreach($argv as $k => $arg){
	if($k == 1){
		$arguments['task'] = $arg;
	}
	elseif($k == 2){
		$arguments['action'] = $arg;
	}
	elseif($k >= 3){
		$params[] = $arg;
	}
}
if(count($params) > 0){
	$arguments['params'] = $params;
}

define('CURRENT_TASK', $argv[1] ?? null);
define('CURRENT_ACTION', $argv[2] ?? null);

try {
	$console->handle($arguments);
}
catch (\Phalcon\Exception $e) {
	echo $e->getMessage();
	exit(255);
}

