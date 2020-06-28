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

require_once( dirname(__FILE__) . '/Log.class.php');

/** Output fatal errors as JSON */
function fatal_error_handler($buffer){
	$error = error_get_last();

	if (!is_null($error)) {
		Log::write("PHP Error: " . $error['message'] . ' at ' . $error['file'] . ':' . $error['line']);
		ResponseWriter::setStatus(500, "Backend Error");
		header('Content-Type: application/json');
		return json_encode(array(
			'error' => 'Backend Error',
			'code' => 500,
			'file' => $error['file'],
			'line' => $error['line'],
			'message' => $error['message']
		));
	}
	return $buffer;
}
