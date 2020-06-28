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

interface iRequest {

	/** Returns the path for this request */
	public function getPath();

	/** Returns the params for this path */
	public function getParams();

	/** Returns the method */
	public function getMethod();

	/** Returns the query params */
	public function getQuery();

	/** Returns the input */
	public function getInput();

}
