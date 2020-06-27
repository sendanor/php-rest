<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko-Heikki Heusala <jheusala@iki.fi>
 */

namespace SimpleREST;

use Exception;

class Response {

	private static $statusCode = null;
	private static $statusMessage = null;
        private static $headers = null;

	/** Get JSON response */
	public static function getJSONResponse ($data) {

		return json_encode($data);

	}

	public static function outputString ($data) {

		self::outputHeaders();
		self::outputStatus();
		echo $data . "\n";

	}

	/** Write JSON response */
	public static function outputJSON ($data) {

		self::setHeader('Content-Type', 'application/json');

		self::outputString( self::getJSONResponse($data) );

	}

	/** */
	public static function isHeadersSent () {

		if (headers_sent()) {
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

		if (self::$statusCode !== null) {
			throw new Exception('Headers already set!');
		}

		if (is_null($message)) {
			$message = self::getMessageForCode($code);
		}

		self::$statusCode = $code;
		self::$statusMessage = $message;

	}

	/** Write response */
	public static function output ( $data ) {

		if ( $data instanceof Exception ) {
			return self::outputException( $data );
		}

		self::outputJSON($data);

	}

	/** Output headers */
	protected static function outputHeaders () {

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

	protected static function outputStatus () {

		$code = self::$statusCode == null ? 200 : self::$statusCode;
		$message = self::$statusMessage == null ? HTTPStatusMessages::getMessage($code) : self::$statusMessage;

		header("Status: $code $message");
		header("HTTP/1.0 $code $message");

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
