<?php
/* 
 * Sendanor SimpleREST PHP Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi> 
 */

namespace SimpleREST\Framework;

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
		if($this->error instanceof \SimpleREST\HTTPError) {
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
