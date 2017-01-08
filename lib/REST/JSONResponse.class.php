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

	/** Output data as JSON */
	public function output($data) {
		header('Content-Type: application/json');
		echo json_encode($data) . "\n";
	}

}

