<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */

namespace SimpleREST;

use Exception;
use Throwable;

class Response {

	private static $statusCode = null;
	private static $statusMessage = null;
  private static $headers = null;
  private static $output_sent = false;

  /**
   * @return bool
   */
  public static function isSent () {
    return self::$output_sent;
  }

  /**
   * Get JSON response
   *
   * @param $data mixed
   * @return string
   * @throws Exception if JSON encoding fails
   */
	public static function getJSONResponse ($data) {

	  try {

      $data = json_encode($data);

      if ($data === "") {
        throw new Exception('Empty string from JSON encoding!');
      }

      return $data;

    } catch(Throwable $e) {
	    Log\error("ERROR: ", $e);
	    throw new Exception('JSON encoding failed!');
    }

	}

  /**
   * @param $data mixed
   * @throws Exception if headers have already been sent
   */
	public static function outputString ($data) {

	  Log\debug('Sending response headers...');

		self::_outputHeaders();

    Log\debug('Sending response status...');

		self::_outputStatus();

		$len = strlen($data);

    if ($len > 255) {
      Log\debug('Sending response data (' . $len . ' length)...');
    } else {
      Log\debug('Sending response data (' . $len . ' length: "'.$data.'")...');
    }

    echo "" . $data . "\n";

    self::$output_sent = true;

	}

  /**
   * Write JSON response
   *
   * @param $data mixed
   * @throws Exception if headers were already sent
   */
	public static function outputJSON ($data) {

		self::setHeader('Content-Type', 'application/json');

		self::outputString( self::getJSONResponse($data) );

	}

	/** */
	public static function isHeadersSent () {

		return !!headers_sent();

	}

	public static function getMessageForCode ($code) {
		return HTTPStatusMessages::getMessage($code);
	}

  /**
   * @param int $code
   * @param string|null $message
   * @throws Exception if headers sent
   */
	public static function setStatus ($code, $message = null) {

		if ( self::isHeadersSent() ) {
			throw new Exception('Headers already sent!');
		}

		if (self::$statusCode !== null) {
			throw new Exception('Headers already set!');
		}

		if ( $message === null ) {
			$message = self::getMessageForCode($code);
		}

		self::$statusCode = $code;
		self::$statusMessage = $message;

	}

  /**
   * Write response
   *
   * @param $data mixed
   * @throws Exception if headers already sent
   */
	public static function output ( $data ) {

		if ( $data instanceof Exception ) {

			self::outputException( $data );

		} else {

      self::outputJSON($data);

    }

	}

  /**
   * Output headers
   *
   * @throws Exception is headers have been sent
   */
	protected static function _outputHeaders () {

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

  /**
   *
   */
	protected static function _outputStatus () {

		$code = self::$statusCode == null ? 200 : self::$statusCode;
		$message = self::$statusMessage == null ? HTTPStatusMessages::getMessage($code) : self::$statusMessage;

		header("Status: $code $message");
		header("HTTP/1.0 $code $message");

	}

  /**
   * Set multiple headers
   *
   * @param $headers array
   */
	public static function setHeaders ($headers) {

		if ( self::$headers == null ) {
			self::$headers = array();
		}

		foreach( $headers as $key => $value ) {
			self::$headers[$key] = $value;
		}

	}

  /**
   * Get single header
   *
   * @param $key string
   * @return mixed|null
   */
	public static function getHeader ($key) {

		if ( self::$headers == null ) {
			return null;
		}

		return isset( self::$headers[$key] ) ? self::$headers[$key] : null;

	}

  /**
   * Set single header
   *
   * @param $key string
   * @param $value string
   */
	public static function setHeader ($key, $value) {

		if ( self::$headers === null ) {

			$obj = array();
			$obj[$key] = $value;
			self::$headers = $obj;

		} else {

			self::$headers[$key] = $value;

		}

	}

  /**
   * @param int $code
   * @param string|null $message
   * @return array
   */
	public static function getErrorResponse ($code, $message = null) {

		if ($message === null) {
			$message = self::getMessageForCode($code);
		}

		return array(
			'code' => $code,
			'error' => $message
		);

	}

  /**
   * @param Throwable $e
   * @return array
   */
	public static function getExceptionResponse (Throwable $e) {

		if ($e instanceof HTTPError) {
			return self::getErrorResponse( $e->getCode(), $e->getMessage() );
		}

		return self::getErrorResponse( 500, 'Backend Error');

	}

  /**
   * @param array $response
   * @throws Exception if headers already sent
   */
	public static function outputErrorResponse ($response) {

		self::setStatus($response['code'], $response['error']);

		self::output( $response );

	}

  /**
   * @param int $code
   * @param string|null $message
   * @throws Exception if headers already sent
   */
	public static function outputError ($code, $message = null) {

		self::outputErrorResponse( self::getErrorResponse($code, $message) );

	}

  /**
   * @param Throwable $e
   * @throws Exception if headers already sent
   */
	public static function outputException (Throwable $e) {

    self::outputErrorResponse( self::getExceptionResponse($e) );

	}

}
