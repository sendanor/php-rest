<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */

namespace SimpleREST;

use Exception;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

/**
 * Exception class for HTTP errors
 */
class HTTPError extends Exception {

  /**
   * HTTPError constructor.
   *
   * @param int $code HTTP status code
   * @param null $message HTTP status message
   * @param Exception|null $previous
   * @throws Exception
   */
	public function __construct ($code = 500, $message = null, Exception $previous = null) {

		if(is_null($message)) {
			$message = HTTPStatusMessages::getMessage($code);
		}

		if(!is_string($message)) {
			throw new Exception('wrong argument type for message in HTTPError', 0, $this);
		}

		if(!is_long($code)) {
			throw new Exception('wrong argument type for code in HTTPError', 0, $this);
		}

		parent::__construct($message, $code, $previous);

	}

  /**
   * Stringify it
   *
   * @return string
   */
	public function __toString () {
		return "{$this->code} {$this->message}";
	}

}
