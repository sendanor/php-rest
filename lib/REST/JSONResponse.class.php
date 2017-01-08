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
