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
