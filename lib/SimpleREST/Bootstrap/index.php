<?php

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