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
class Redirect extends Response {

	/** The path to redirect the request */
	private $path = null;

	/** Construct the redirect */
	public function __construct ($path) {
		if (!is_string($path)) {
			throw new Exception('Path for redirect must be a string!');
		}
		$this->path = $path;
	}

	/** Get status code */
	public function getStatus () {
		return 307;
	}

	/** Set headers */
	public function getHeaders () {
		return array('Location' => $this->path);
	}

	/** The content for the reply */
	public function getContent () {
		return array('redirect' => $this->path);
	}

}
