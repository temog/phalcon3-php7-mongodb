<?php

use Phalcon\Mvc\User\Component;
use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\Logger\Formatter\Line as LineFormatter;

class Logger extends Component {

	private static $error;
	private static $info;
	private static $warn;

	private static function initError(){
		self::$error = new FileAdapter(__DIR__ . '/../logs/error_' . date("Y-m-d") . '.log');
		self::$error->setFormatter(new customFormatter());
	}

	private static function initInfo(){

		self::$info = new FileAdapter(__DIR__ . '/../logs/info_' . date("Y-m-d") . '.log');
		self::$info->setFormatter(new customFormatter());
	}

	private static function initWarning(){

		self::$warn = new FileAdapter(__DIR__ . '/../logs/warn_' . date("Y-m-d") . '.log');
		self::$warn->setFormatter(new customFormatter());
	}

	public static function error($message){

		if(! self::$error){
			self::initError();
		}


		self::$error->error(self::convertMessage($message));
	}

	public static function info($message){

		if(! self::$info){
			self::initInfo();
		}

		self::$info->log(self::convertMessage($message), \Phalcon\Logger::INFO);
	}

	public static function warn($message){

		if(! self::$warn){
			self::initWarning();
		}

		self::$warn->log(self::convertMessage($message), \Phalcon\Logger::WARNING);
	}

	public static function convertMessage($message){

		if(is_array($message)){
			$text = null;
			foreach($message as $m){
				$text .= ' [' . print_r($m, true) . ']';
			}
		}
		else{
			$text = $message;
		}

		return $text;
	}
}

class customFormatter extends Phalcon\Logger\Formatter
	implements Phalcon\Logger\FormatterInterface {

	public function format($message, $type, $timestamp, $context = null) {

		return date("[Y-m-d H:i:s]", $timestamp).
			' [' . $this->getTypeString($type) . ']' .
			' [' . ($_SERVER['REMOTE_ADDR'] ?? '') . ']' . $message . "\n";
	}
}

