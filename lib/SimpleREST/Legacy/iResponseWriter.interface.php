<?php
/* 
 * Sendanor SimpleREST PHP Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi> 
 */

namespace SimpleREST\Legacy;

use Exception;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

interface iResponseWriter {
	public function setDefaultResponse($type);
	public function output ($data);
	public function outputException (Exception $e);
}
