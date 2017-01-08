<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017 Jaakko-Heikki Heusala <jhh@sendanor.com> 
 */

namespace REST;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

/** */
class ExceptionResponse extends Response {

	protected $error = NULL;

	public function __construct ($e) {
		$this->error = $e;
	}

	public function getStatus () {
		if($this->error instanceof HTTPError) {
			return array($this->error->getCode(), $this->error->getMessage());
		}
		return array(500, 'Backend Error');
	}

	public function getContent () {
		return array(
			'error' => $this->error->getMessage(),
			'code' => $this->error->getCode()
		);
	}

}
