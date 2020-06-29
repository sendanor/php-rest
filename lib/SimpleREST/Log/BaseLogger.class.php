<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\Log;

/**
 * Class BaseLogger
 *
 * @package SimpleREST\Log
 */
abstract class BaseLogger {

  /**
   * Print error log message
   *
   * @param $msg mixed[]
   * @return void
   */
  abstract public function error (...$msg);

  /**
   * Print warning log message
   *
   * @param $msg mixed[]
   * @return void
   */
  abstract public function warning (...$msg);

  /**
   * Print info level log message
   *
   * @param $msg mixed[]
   * @return void
   */
  abstract public function info (...$msg);

  /**
   * Print debug level log message.
   *
   * These log messages will not be printed on non-production mode.
   *
   * @param $msg mixed[]
   * @return void
   */
  abstract public function debug (...$msg);

}
