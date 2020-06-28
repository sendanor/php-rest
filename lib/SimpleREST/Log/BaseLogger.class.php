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
   * @param $msg string
   * @return void
   */
  abstract public function error ($msg);

  /**
   * Print warning log message
   *
   * @param $msg string
   * @return void
   */
  abstract public function warning ($msg);

  /**
   * Print info level log message
   *
   * @param $msg string
   * @return void
   */
  abstract public function info ($msg);

}
