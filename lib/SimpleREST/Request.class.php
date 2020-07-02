<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */

namespace SimpleREST;

use Exception;
use TypeError;
use Throwable;
use Closure;
use ReflectionClass;
use ReflectionException;

if (!defined('REST_ROUTE_DOC_COMMENT')) {
  define('REST_ROUTE_DOC_COMMENT', '@Route');
}

class Request {

  /**
   * @var OutputBuffer|null
   */
  private static $_outputBuffer = null;

  /**
   * @var bool
   */
  private static $_started = false;

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
   * Previous error handler if we have enabled our own.
   *
   * @var mixed|null
   */
  private static $_oldErrorHandler = null;

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
   * @param string $search
   * @return string
   * @throws TypeError if $search is invalid or empty string
   */
	public static function normalizeMatchFormat ($search) {

	  if (!is_string($search)) throw new TypeError('argument was not a string');

    $search = trim($search);

    if ( strlen($search) === 0 )  throw new TypeError('argument was empty string');
    if ( $search === "*" )        return "* /*";
    if ( $search === "?" )        return "? /*";
    if ( $search[0] === '/' )     return '* ' . $search;

    if ( strpos($search,'/') === FALSE ) {
      return $search . ' /*';
    }

    return $search;

  }

  /**
   * @param string|array $search Method name as string, or "*" for any, or an array of method names. Must be lowercase.
   * @return bool|array
   * @fixme Implement unit testing
   */
	public static function isMatch ( $search ) {

    // Parse arrays
    if ( is_array($search) ) {
      foreach ($search as $opt) {
        $params = self::isMatch($opt);
        if ( $params !== FALSE ) {
          return $params;
        }
      }
      return FALSE;
    }

    // Invalidate non-strings
    if ( !is_string($search) ) {
      throw new TypeError('Match option was invalid: ' . var_export($search, true) );
    }

    $search = self::_splitPath(self::normalizeMatchFormat($search));

    if ( $search === "* /*" ) return TRUE;

    $current = self::_splitPath( Request::getMethod() . ' ' . Request::getPath() );

    $obj = array();
    $params = array();
    $i = -1;

    //Log\debug('START SEARCH =', $search, 'CURRENT =', $current);

    if (count($current) > count($search) && end($search) !== '*') {
      return FALSE;
    }

    foreach($search as $format) {

      ++$i;

      $item = isset($current[$i]) ? $current[$i] : null;

      //Log\debug($i,'FORMAT =', $format);
      //Log\debug($i,'ITEM =', $item);

      if ( $format === '*' ) {
        //Log\debug($i,'Wildcard, next.');
        continue;
      }

      if ( $item === null ) {
        //Log\debug($i,'ITEM is NULL, fail.');
        return FALSE;
      }

      if ( $format === '?' ) {
        //Log\debug($i,'Variable (unnamed), next.');
        array_push($params, $item);
        continue;
      }

      if ( $format !== '' && $format[0] === ':' ) {
        $key = substr($format, 1);
        //Log\debug($i,"Variable (named: '$key'), next.");
        $obj[$key] = $item;
        continue;
      }

      if ( $format !== $item ) {
        //Log\debug($i,"FORMAT !== ITEM, fail.");
        return FALSE;
      }

      //Log\debug($i,"FORMAT === ITEM, next.");

    }

    if ( count($obj) !== 0 ) {
      array_push($params, $obj);
    }

    //Log\debug("Everything OK, success.");
    return count($params) !== 0 ? $params : TRUE;

  }

  private static function _splitPath ($value) {

	  return array_map(function($item) {
	    $item = trim($item);
      return $item;
    }, explode('/', $value));

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
   * If you mix "?" placeholders and ":key" variables, the last option in the callback will have the object with properties.
   *
   * @param string|array|null $search The match option
   * @param callable $f Calls the function with optional parameters from the matched path.
   * @throws TypeError if match option is invalid
   */
	public static function match ( $search, $f ) {

    //Log\debug("SEARCH =", $search);

	  $params = self::isMatch($search);

    //Log\debug("RESULT =", $params);

    if ($params === TRUE) {
      self::run($f);
    } else if ( $params !== FALSE ) {
      self::run($f, ...$params);
    }

  }

  /**
   * @param string $className
   * @param array $params
   * @throws ReflectionException if the class does not exist
   */
  public static function matchUsingReflectionClass ($className, $params) {

    $obj = null;

    $reflection = new ReflectionClass($className);

    $methods = $reflection->getMethods();

    foreach($methods as &$method) {

      $search = array_filter(array_map(

        function($row) {

          $i = strpos($row, REST_ROUTE_DOC_COMMENT);

          if ($i === FALSE) return NULL;

          $tmp = trim(substr($row, $i + strlen(REST_ROUTE_DOC_COMMENT) ));

          // Enable @Route( ... ) syntax
          $len = strlen($tmp);
          if ( $len >= 2 && $tmp[0] === '(' && $tmp[ $len - 1 ] === ')' ) {
              $tmp = trim(substr($tmp, 1, $len - 2));
          }

          return $tmp;

        },

        explode("\n", $method->getDocComment())
      ), function ($item) {
        return !is_null($item);
      });

      //Log\debug('METHOD COMMENT = ', $search );

      if ($method->IsStatic()) {

        $cb = self::_createCallbackFromStaticReflectionMethod($method);

      } else {

        if ($obj === null) $obj = new $className(...$params);

        $cb = self::_createCallbackFromReflectionMethod($method, $obj);

      }

      Request::match($search, $cb);

    }

  }

  /**
   * @param $method
   * @return Closure
   */
  protected static function _createCallbackFromStaticReflectionMethod (&$method) {

    return function(...$args) use(&$method) {
      return $method->invokeArgs(null, $args);
    };

  }

  /**
   * @param $obj
   * @param $method
   * @return Closure
   */
  protected static function _createCallbackFromReflectionMethod (&$method, &$obj) {

    return function(...$args) use(&$method, &$obj) {
      return $method->invokeArgs($obj, $args);
    };

  }

  protected static function _isClass ($className) {
    return is_string($className) && class_exists($className, TRUE);
  }

  /**
   * Run a request with optional params to the callback.
   *
   * @param callable|string $f The function to call or a class name
   * @param mixed[] $params Optional params to the $f
   */
	public static function run ($f, ...$params) {

    try {

      if (!self::$_started) {
        self::start( self::getDefaultName() );
      }

      if (Response::isSent()) {
        throw new Exception('Response was already sent.');
      }

      if ( self::_isClass($f) ) {

        self::matchUsingReflectionClass($f, $params);

        if (!Response::isSent()) {
          Response::outputError(404);
        }

      } else {

        if (!is_callable($f)) {
          throw new TypeError('Argument is not callable: ' . var_export($f, true) );
        }

        $response = $f(...$params);

        if (!Response::isSent()) {
          Response::output($response);
        }

      }

      exit(0);

    } catch (Exception $e) {
	    self::_handleError($e);
    } catch (Throwable $e) {
	    self::_handleError($e);
    }

  }

  protected static function _handleError ($e) {

    Log\error('Error: ' . $e);

    if (!Response::isSent()) {
      try {
        Response::outputException($e);
      } catch (Exception $e2) {
        Log\error('Exception while printing previous exception: ' . $e2);
      } catch (Throwable $e2) {
        Log\error('Error while printing previous exception: ' . $e2);
      }
    }

    exit(1);

  }

  /**
   * Initialize default request handlers
   *
   * @param string|null $name Optional application name for logging. If not defined, can also be defined using `REST_LOGGER_NAME`.
   * @throws Exception if already started
   */
  public static function start ($name = null) {

    if (self::$_started === true) {
      throw new Exception('Cannot start request again.');
    }

    self::$_started = true;

    require_once( dirname(__FILE__) . '/Log/index.php' );
    Log\setLogger( Log\Manager::createDefaultLogger($name) );
    Log\debug("Request started in development mode.");

    //ob_end_clean();
    //header("Connection: close");
    ignore_user_abort(true);
    error_reporting(0);

    require_once( dirname(__FILE__) . '/OutputBuffer.class.php' );

    self::$_outputBuffer = new OutputBuffer( function ($output) {
      return self::_onOutput($output);
    } );

    self::$_oldErrorHandler = set_error_handler(array("SimpleREST\Request", "_onError"));

    self::enableShutdownHandler();

    require_once( dirname(__FILE__) . '/HTTPError.class.php' );
    require_once( dirname(__FILE__) . '/HTTPStatusMessages.class.php' );
    require_once( dirname(__FILE__) . '/Response.class.php' );

    Log\debug("Request initialized.");

  }

  /**
   * @param int $errno
   * @param string $errorMessage
   * @param string $errorFile
   * @param int $errorLine
   * @param array $errorContext
   */
  protected static function _onError ($errno, $errorMessage, $errorFile, $errorLine, $errorContext) {

    Log\error("ERROR: ", $errno, $errorMessage, $errorFile, $errorLine, $errorContext);

  }

  /**
   * @param string $output
   * @return string
   */
  protected static function _onOutput ($output) {

    if (!self::_isJson($output)) {

      Log\error('The output was not valid JSON:', $output, '(' . strlen($output) . ' bytes)');

      $output = json_encode( Response::getErrorResponse(500, "Internal Server Error") );

    } else {

      Log\debug('The output was valid JSON (' . strlen($output) . ' bytes)');

    }

    // This will make browser(s) end the connection as soon as everything has been delivered
    header('Content-Length: ' . strlen($output) );

    return $output;

  }

  /**
   * @param string $string
   * @return bool
   */
  private static function _isJson(string $string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
  }

  /**
   * Enable shutdown handler.
   *
   * See also `Request::start()`
   */
  public static function enableShutdownHandler () {
	  register_shutdown_function('\SimpleREST\Request::onShutdown');
  }

  /**
   * @return bool
   */
  public static function isProduction () {
    return defined('REST_PRODUCTION') && REST_PRODUCTION === true;
  }

  /**
   * @return string
   */
  public static function getDefaultName () {

    return defined('REST_NAME') ? REST_NAME : self::getDefaultLoggerName();

  }

  /**
   * @return string
   */
  public static function getDefaultLoggerName () {

    return defined('REST_LOGGER_NAME') ? REST_LOGGER_NAME : 'unnamed';

  }

  /**
   * Shutdown function to catch fatal errors and output them as JSON.
   *
   * See `Request::enableShutdownHandler()`
   *
   */
  public static function onShutdown () {

    Log\debug("Shutdown handler called.");

    // Handle (fatal) PHP errors
    $error = error_get_last();
    if ( $error !== null ) {

      Log\error("PHP Error: " . $error['message'] . ' at ' . $error['file'] . ':' . $error['line']);

      if (Response::isHeadersSent()) {
        Log\warning("Warning! Headers were already sent!");
        return;
      }

      try {

        Log\debug("Sending error response.");
        Response::setStatus(500, "Backend Error");
        Response::setHeader('Content-Type', 'application/json');

        if (self::isProduction()) {

          Response::output(array(
            'error' => 'Backend Error',
            'code' => 500
          ));

        } else {

          Response::output(array(
            'error' => 'Backend Error',
            'code' => 500,
            'file' => $error['file'],
            'line' => $error['line'],
            'message' => $error['message']
          ));

        }

        exit(1);

      } catch (Exception $e) {

          Log\error("Exception while preparing the response for previous fatal error: " . $e);

      } catch (Throwable $e) {

          Log\error("Error while preparing the response for previous fatal error: " . $e);

      }

    } else {
      Log\debug('No errors detected.');
    }

  }

}
