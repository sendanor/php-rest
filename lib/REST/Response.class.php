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

require_once( dirname(__FILE__) . '/fatal_error_handler.php');

/** */
abstract class Response implements iResponse {

	/** Constructor */
	function __construct () {
		ini_set('display_errors', 0);
		ob_start('REST\fatal_error_handler');
	}

	/** Set HTTP status code */
	static public function setStatus ($code, $message) {
		if(headers_sent()) {
			Log::write('Warning! Headers were sent already, could not send.');
			return;
		}
		if(!defined('HTTPErrorStatus')) {
			//Log::write('Setting status code ' . $code);
			define('HTTPErrorStatus', TRUE);
			header("Status: $code $message");
			header("HTTP/1.0 $code $message");
		}
	}

	/** Set HTTP status code */
	public function outputStatus ($code, $message) {
		self::setStatus($code, $message);
	}

	/** Set HTTP headers */
	public function outputHeaders ($code, $message) {
		if(headers_sent()) {
			Log::write('Warning! Headers were sent already, could not send.');
			return;
		}
		self::outputStatus($code, $message);
		header("Access-Control-Allow-Origin: *");
	}

	/** Output data as JSON */
	//abstract public function output (array $data);

	/** */
	public function outputException (Exception $e) {
		$code = $e->getCode();
		$message = $e->getMessage();

		if($e instanceof HTTPError) {
			$this->outputHeaders($code, $message);
		} else {
			$this->outputHeaders(500, 'Backend Error');
		}

		$this->output(array(
			'error' => $message,
			'code' => $code
		));
	}

	/** End response */
	public function __destruct () {
		ob_end_flush();
	}

}
