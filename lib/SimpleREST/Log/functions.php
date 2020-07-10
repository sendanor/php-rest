<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */
declare(strict_types=1);

namespace SimpleREST\Log;

require_once( dirname(dirname(__FILE__)) . '/Bootstrap/index.php' );

use Exception;
use TypeError;
use function SimpleREST\Bootstrap\isProduction;

/**
 * @param $msg string
 * @throws Exception
 */
function error (...$msg) {
  Manager::getLogger()->error(...$msg);
}

/**
 * @param $msg string
 * @throws Exception
 */
function warning (...$msg) {
  Manager::getLogger()->warning(...$msg);
}

/**
 * @param $msg string
 * @throws Exception
 */
function info (...$msg) {
  Manager::getLogger()->info(...$msg);
}

/**
 * @param $msg string
 * @throws Exception
 */
function debug (...$msg) {
  if (! isProduction() ) {
    Manager::getLogger()->debug(...$msg);
  }
}

/**
 * @param array $args
 * @return string
 */
function stringifyValues (...$args) {

  if ( count($args) >= 2 ) {
    return implode(" ", array_map(function($item) {
      return stringifyValues($item);
    }, $args));
  }

  $args = array_shift($args);

  if (is_string($args)) return $args;

  if (method_exists($args, '__toString')) {
    return "" . $args;
  }

  return var_export($args, true);

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
