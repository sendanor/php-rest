<?php
/* 
 * Sendanor SimpleREST PHP Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi> 
 */

namespace SimpleREST;

use Exception;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

/**
 * Shutdown function to catch fatal errors and output them as JSON.
 *
 * Use it like this: `register_shutdown_function('SimpleREST\onShutdown')`
 *
 * @return false|string
 */
function onShutdown () {

	$error = error_get_last();

	if ( $error !== null ) {

		Log\error("PHP Error: " . $error['message'] . ' at ' . $error['file'] . ':' . $error['line']);

		try {

      Response::setStatus(500, "Backend Error");

      Response::setHeader('Content-Type', 'application/json');

      return json_encode(array(
        'error' => 'Backend Error',
        'code' => 500,
        'file' => $error['file'],
        'line' => $error['line'],
        'message' => $error['message']
      ));

    } catch (Exception $e) {
      Log\error("Exception while preparing the response for previous fatal error: " . $e);
    }

	}

}
