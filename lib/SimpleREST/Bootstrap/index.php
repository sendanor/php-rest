<?php
declare(strict_types=1);

namespace SimpleREST\Bootstrap;

/**
* @return string
*/
function getPath () {

  if ( isset($_SERVER['PATH_INFO']) && strlen($_SERVER['PATH_INFO']) >= 1 ) {

    return $_SERVER['PATH_INFO'];

  } else if ( isset($_SERVER['ORIG_PATH_INFO']) ) {

    return $_SERVER['ORIG_PATH_INFO'];

  }

  return '/';

}

/**
 * Returns TRUE if $str starts with $value.
 *
 * @param string $str
 * @param string $value
 * @return bool
 */
function startsWith (string $str, string $value) {
  return substr($str, 0, strlen($value)) === $value;
}

/**
 * @param string $path
 * @return bool
 */
function pathStartsWith (string $path) {

  return startsWith(getPath(), $path);

}

/**
 * @return bool
 */
function isProduction () {
  return defined('REST_PRODUCTION') && REST_PRODUCTION === true;
}

/**
 * @return string
 */
function getDefaultLoggerName () {

  return defined('REST_LOGGER_NAME') ? REST_LOGGER_NAME : 'unnamed';

}

/**
 * @return string
 */
function getDefaultName () {

  return defined('REST_NAME') ? REST_NAME : getDefaultLoggerName();

}
