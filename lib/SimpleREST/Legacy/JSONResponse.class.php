<?php
/* 
 * Sendanor SimpleREST PHP Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi> 
 */

namespace SimpleREST\Legacy;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

/** */
class JSONResponse extends Response {

	protected $data = NULL;

	public function __construct ($data) {
		$this->data = $data;
	}

	public function getHeaders () {
		return array('Content-Type' => 'application/json');
	}

	public function getContent () {
		return json_encode($this->data) . "\n";
	}

}
