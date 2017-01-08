<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017 Jaakko-Heikki Heusala <jhh@sendanor.com> 
 */

namespace REST;

use Exception;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

interface iResponse {

	public function outputStatus ($code, $message);
	public function outputHeaders ($code, $message);
	public function output ($data);
	public function outputException (Exception $e);

}
