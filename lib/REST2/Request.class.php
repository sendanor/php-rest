<?php

namespace REST2;

use Exception;

class Request {

	private static $status = null;
        private static $method = null;
        private static $path = null;
        private static $input = null;
        private static $headers = null;

	/** Returns current method name, all lowercase characters. */
	public static function getMethod () {
		if (self::$method == null) {
			self::$method = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'get';
		}
		return self::$method;
	}

	/**
	 */
	public static function isMethodGet() {
		return self::getMethod() === 'get';
	}

	/** Returns current request path */
	public static function getPath () {
		if (self::$method == null) {
			self::$path = isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '/';
		}
		return self::$path;
	}

	/** Returns current request query params */
	public static function getQueryParams () {
		return (array)$_GET;
	}

	/** Returns current request input as JSON */
	public static function getInput () {

		if ( self::$input == null ) {
			if ( self::getMethodGet() ) {
				self::$input = array();
			} else {
				self::$input = json_decode(file_get_contents('php://input'), true);
			}
		}

		return self::$input;

	}

	/** Get JSON response */
	public static function getJSONResponse ($data) {

		return json_encode($data);

	}

	public static function outputString ($data) {

		self::outputHeaders();

		echo $data . "\n";

	}

	/** Write JSON response */
	public static function outputJSONResponse( $data ) {

		self::setHeader('Content-Type', 'application/json');

		self::outputString( self::getJSONResponse($data) );

	}

	/** */
	public static function isHeadersSent () {

		if (headers_sent()) {
			return true;
		}

		if (self::$status !== null) {
			return true;
		}

		return false;

	}

	public static function getMessageForCode ($code) {
		return HTTPStatusMessages::getMessage($code);
	}

	/** */
	public static function setStatus ($code, $message = null) {

		if ( self::isHeadersSent() ) {
			throw new Exception('Headers already sent!');
		}

		if (is_null($message)) {
			$message = self::getMessageForCode($code);
		}

		self::$status = $code;

		header("Status: $code $message");
		header("HTTP/1.0 $code $message");

	}

	/** Write response */
	public static function output ( $data ) {

		if ( $data instanceof Exception ) {
			return self::outputException( $data );
		}

		self::outputJSONResponse($data);

	}

	/** Output headers */
	public static function outputHeaders () {

		if ( self::isHeadersSent() ) {
			throw new Exception('Headers already sent!');
		}

		if ( self::$headers == null ) {
			self::$headers = array();
		}

		foreach( self::$headers as $key => $value ) {
			header( $key . ': ' . $value );
		}

	}

	/** */
	public static function setHeaders ($headers) {

		if ( self::$headers == null ) {
			self::$headers = array();
		}

		foreach( $headers as $key => $value ) {
			self::$headers[$key] = $value;
		}

	}

	/** Get header */
	public static function getHeader ($key) {

		if ( self::$headers == null ) {
			return null;
		}

		return isset( self::$headers[$key] ) ? self::$headers[$key] : null;

	}

	/** */
	public static function setHeader ($key, $value) {

		if ( self::$headers == null ) {
			$obj = array();
			$obj[$key] = $value;
			self::$headers = $obj;
		} else {
			self::$headers[$key] = $value;
		}

	}

	/** */
	public static function getErrorResponse ($code, $message = null) {

		if (is_null($message)) {
			$message = self::getMessageForCode($code);
		}

		return array(
			'code' => $code,
			'error' => $message
		);

	}

	/** */
	public static function getExceptionResponse (Exception $e) {

		if ($e instanceof HTTPError) {
			return self::getErrorResponse( $e->getCode(), $e->getMessage() );
		}

		return self::getErrorResponse( 500, 'Backend Error');

	}

	/** */
	public static function outputError ($code, $message = null) {

		$response = self::getErrorResponse($code, $message);

		self::setStatus($response['code'], $response['error']);

		return self::output( $response );

	}

	/** */
	public static function outputException (Exception $e) {

		self::outputError( self::getExceptionResponse($e) );

	}

}
