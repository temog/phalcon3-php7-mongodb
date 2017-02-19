<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{

	public function beforeExecuteRoute($dispatcher){

		$this->view->setVar('version', $this->config->version);

	}
}

