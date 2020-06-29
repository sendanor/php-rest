<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */

namespace SimpleREST;

use Exception;
use TypeError;

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
   * Match path and/or methods with optional parameters.
   *
   * If a match is made, will execute `Request::run($f[, $opt(s)])`, which will also terminate the process.
   *
   * Eg. if this function ever returns, no matches were made.
   *
   * Examples for match option:
   *
   *   "*"                        - Any method or path
   *   "?"                        - Any method or path and place the method in the first argument to $f
   *   ":method"                  - Any method or path and place the method in the property named "method" for $f
   *   "/foo"                     - Any method for path /foo
   *   "/foo/*"                   - Any method for path /foo/*
   *   "/foo/:variable"           - Any method for path /foo/* and place the wildcard value in property named "variable"
   *   "/foo/?"                   - Any method for path /foo/* and place the wildcard value in first argument to $f
   *   "put"                      - PUT method for any path
   *   "get /foo"                 - GET method for path /foo
   *   ["get", "head"]            - GET or HEAD method for any path
   *   ["get /foo", "head /foo"]  - GET or HEAD method for path /foo
   *
   * @param string|array|null $search The match option
   * @param callable $f Calls the function with optional parameters from the matched path.
   * @throws TypeError if match option is invalid
   */
	public static function match ( $search, callable $f ) {

	  // Parse arrays
	  if ( is_array($search) ) {
	    foreach ($search as $opt) {
	      self::match($opt, $f);
      }
	    return;
    }

	  // Invalidate non-strings
	  if ( !is_string($search) ) {
	    throw new TypeError('Match option was invalid: ' . var_export($search, true) );
    }

	  $search = trim($search);

    if ( $search === "*" ) {
      self::run($f);
      return;
    }

	  if ($search[0] === '/') {
	    $search = '*' . $search;
    }

	  if ( strpos($search,'/') === FALSE ) {
	    $search = $search . '/*';
    }

    $current = Request::getMethod() . '/' . Request::getPath();

	  SimpleREST\Log\info('search: ', var_export($search, true));
	  SimpleREST\Log\info('current: ', var_export($current, true));

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
