<?php
/*
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\Log;

/**
 * Class Log to wrap our global log functions outside of Log package.
 *
 * @package SimpleREST\Log
 */
class Log {

  public static function error (...$msg) {
    error(...$msg);
  }

  public static function warning (...$msg) {
    warning(...$msg);
  }

  public static function info (...$msg) {
    info(...$msg);
  }

  public static function debug (...$msg) {
    debug(...$msg);
  }

  public static function setLogger ($logger) {
    setLogger($logger);
  }

}
