<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */

namespace SimpleREST;

use Exception;

class Request {

  /**
   * @var string|null
   */
  private static $_method = null;

  /**
   * @var string|null
   */
  private static $_path = null;

  /**
   * @var mixed|null
   */
  private static $_input = null;

  /**
   * @var bool
   */
  private static $_inputFetched = false;

  /**
   * Returns current method name, all lowercase characters.
   *
   * Defaults to "get".
   *
   * @return string
   */
	public static function getMethod () {

		if (self::$_method === null) {
			self::$_method = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'get';
		}

		return self::$_method;

	}

  /**
   * Returns true if method is get.
   *
   * @return bool
   */
	public static function isMethodGet() {
		return self::getMethod() === 'get';
	}

  /**
   * Returns current request path.
   *
   * Defaults to "/".
   *
   * @return string
   */
	public static function getPath () {

		if (self::$_path == null) {

			if ( isset($_SERVER['PATH_INFO']) && strlen($_SERVER['PATH_INFO']) >= 1 ) {

				self::$_path = $_SERVER['PATH_INFO'];

			} else if ( isset($_SERVER['ORIG_PATH_INFO']) ) {

				self::$_path = $_SERVER['ORIG_PATH_INFO'];

			} else {

				self::$_path = "/";

			}

		}

		return self::$_path;

	}

  /**
   * Returns current request query params.
   *
   * @return array
   */
	public static function getQueryParams () {
		return (array)$_GET;
	}

  /**
   * Returns current request input as JSON
   *
   * @return array|mixed
   */
	public static function getInput () {

		if ( !self::$_inputFetched ) {

			if ( self::isMethodGet() ) {
				self::$_input = array();
			} else {
				self::$_input = json_decode(file_get_contents('php://input'), true);
			}

      self::$_inputFetched = true;

		}

		return self::$_input;

	}

  /**
   * @param string|array $methods Method name as string, or "*" for any, or an array of method names. Must be lowercase.
   * @param string $path
   * @return bool
   */
	public static function isMatch ( $methods, $path ) {

	  if (Request::getPath() !== $path) {
	    return false;
    }

    if ($methods === "*") return true;

    $request_method = Request::getMethod();

	  if ( $methods === $request_method ) return true;

	  if (!is_array($methods)) return false;

    foreach ($methods as $method) {
      if( $method === "*") return true;
      if( $method === $request_method ) return true;
    }

    return false;

  }

  /**
   * @param string|array $methods
   * @param string $path
   * @param callable $f
   */
	public static function match ( $methods, $path, callable $f ) {

	  if ( self::isMatch($methods, $path) ) {
	    self::run($f);
    }

  }

  /**
   * @param $f callable
   */
	public static function run ($f) {

	  try {

	    $response = $f();

      if (!Response::isSent()) {
        Response::output($response);
      }

      exit(0);

    } catch (Exception $e) {

      Log\error('Exception: ' . $e);

      if (!Response::isSent()) {
        try {
            Response::outputException($e);
        } catch (Exception $e2) {
          Log\error('Exception while printing previous exception: ' . $e2);
        }
      }

      exit(1);

    }

  }

}
