<?php
/*
 * Sendanor's SimplePHP Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\Framework;

use Exception;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

require_once( dirname(__FILE__) . '/fatal_error_handler.php');

/** */
class ResponseWriter implements iResponseWriter {

	/** Defaults headers */
	protected $headers = NULL;

	/** The default response type */
	protected $default_type = 'SimpleREST\Framework\JSONResponse';

	/** Constructor */
	function __construct () {
		$this->headers = array();
		ini_set('display_errors', 0);
		ob_start('SimpleREST\Framework\fatal_error_handler');
	}

	/** Set a default headers */
	public function setDefaultHeaders (array $headers) {
		if (is_null($this->headers)) {
			$this->headers = array();
		}
		foreach( $headers as $key => $value ) {
			$this->headers[$key] = $value;
		}
	}

	/** Set a default response class */
	public function setDefaultResponse ($type) {
		if (!is_string($type)) {
			throw new Exception('Response type must be a string!');
		}
		$this->default_type = $type;
	}

	/** Set HTTP status code */
	static public function setStatus ($code, $message = null) {

		try {
			\SimpleREST\setStatus($code, $message);
		} catch (Exception $e) {
			Log::write('Warning! Failed to set headers: '. $e);
		}

	}

	/** Set HTTP status code */
	public function outputStatus ($input) {
		$message = NULL;

		if(is_array($input)) {
			$code = array_shift($input);
			$message = array_shift($input);
		} else {
			$code = $input;
		}

		if ( (!is_null($code)) && $code) {
			self::setStatus($code, $message);
		}
	}

	/** Set HTTP headers */
	public function outputHeaders (array $headers) {
		if(headers_sent()) {
			Log::write('Warning! Headers were sent already, could not send.');
			return;
		}
		foreach( $headers as $key => $value ) {
			header($key . ': ' . $value);
		}
	}

	/** Output data as JSON */
	public function output ($data) {

		//Log::write( "data = " . var_export($data, true) );

		if ($data instanceof Exception) {
			return $this->outputException($data);
		}

		$input_is_response = $data instanceof iResponse;

		$headers = is_array($this->headers) ? $this->headers : array();
		//Log::write( "1. headers = " . var_export($headers, true) );

		// Turn it into iResponse, if it isn't already
		if (!$input_is_response) {
			$data = new $this->default_type($data);
		}

		$status = $data->getStatus();
		$next_headers = $data->getHeaders();
		//Log::write( "next_headers = " . var_export($next_headers, true) );
		if(is_array($next_headers)) {
			$headers = array_merge($headers, $next_headers);
			//Log::write( "2. headers = " . var_export($headers, true) );
		}
		$content = $data->getContent();
		//Log::write( "status = " . var_export($status, true) );
		//Log::write( "headers = " . var_export($headers, true) );
		//Log::write( "content = " . var_export($content, true) );

		// Turn content into default response if it's not a string, and we didn't just convert it.
		if ( (!is_string($content)) && $input_is_response) {
			//Log::write( "1 default type: " . $this->default_type);

			$data = new $this->default_type($content);
			$status = $status ? $status : $data->getStatus();
			$next_headers = $data->getHeaders();
			//Log::write( "next_headers = " . var_export($next_headers, true) );
			if(is_array($next_headers)) {
				$headers = array_merge($headers, $next_headers);
				//Log::write( "1. headers = " . var_export($headers, true) );
			}
			$content = $data->getContent();

			//Log::write( "1 status = " . var_export($status, true) );
			//Log::write( "1 headers = " . var_export($headers, true) );
			//Log::write( "1 content = " . var_export($content, true) );

		}

		// Turn content into JSON if it's still not a string
		if (!is_string($content)) {
			$data = new JSONResponse($content);
			$status = $status ? $status : $data->getStatus();
			$next_headers = $data->getHeaders();
			//Log::write( "next_headers = " . var_export($next_headers, true) );
			if(is_array($next_headers)) {
				$headers = array_merge($headers, $next_headers);
				//Log::write( "2. headers = " . var_export($headers, true) );
			}
			$content = $data->getContent();

			//Log::write( "2 status = " . var_export($status, true) );
			//Log::write( "2 headers = " . var_export($headers, true) );
			//Log::write( "2 content = " . var_export($content, true) );

		}

		//Log::write( "3 status = " . var_export($status, true) );
		//Log::write( "3 headers = " . var_export($headers, true) );
		//Log::write( "3 content = " . var_export($content, true) );
	
		$this->outputStatus($status);

		if (is_array($headers)) {
			$this->outputHeaders($headers);
		}

		echo "".$content;
	}

	/** */
	public function outputException (Exception $e) {
		return $this->output( new ExceptionResponse($e) );
	}

	/** End response */
	public function __destruct () {
		ob_end_flush();
	}

}
