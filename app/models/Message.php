<?php

use Phalcon\Mvc\User\Component;

class Message extends Component {

	public static function common($key){

		$message = [
			'errorSystem' => 'システムエラーが発生しました',
			'errorCreate' => 'insert failed',
			'errorUpsert' => 'insert or update failed',
			'invalidParameter' => 'invalid param',
		];

		return $message[$key];
	}
}

