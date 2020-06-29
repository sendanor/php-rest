<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\Log;

use Exception;
use SimpleREST\Request;
use TypeError;

/**
 * @param $msg string
 */
function error (...$msg) {
  Manager::getLogger()->error(...$msg);
}

/**
 * @param $msg string
 */
function warning (...$msg) {
  Manager::getLogger()->warning(...$msg);
}

/**
 * @param $msg string
 */
function info (...$msg) {
  Manager::getLogger()->info(...$msg);
}

/**
 * @param $msg string
 */
function debug (...$msg) {
  if (! Request::isProduction() ) {
    Manager::getLogger()->debug(...$msg);
  }
}

/**
 * @param mixed[] $args
 * @return string
 */
function stringifyValues (array $args) {
  return implode(" ", array_map(function($item) {

    if (is_string($item)) return $item;

    return var_export($item, true);
  }, $args));
}

/**
 * @param $logger
 */
function setLogger ($logger) {
  Manager::setLogger( $logger );
}

/**
 * Creates a logger by name and argument for logger constructor
 *
 * @param string $name The name of logger
 * @param string $opt The option for logger
 * @return BaseLogger
 * @throws TypeError if $name is unknown logger
 * @throws Exception if $opt is not a string
 */
function createLogger ($name, $opt) {

  switch ($name) {

    case "syslog":
      require_once( dirname(__FILE__) . '/Syslog/index.php');
      return new Syslog($opt);

    case "stderr":
      require_once( dirname(__FILE__) . '/ErrorLog/index.php');
      return new ErrorLog($opt);

    default:
      throw new TypeError('Unknown logger: ' . $name);

  }

}
