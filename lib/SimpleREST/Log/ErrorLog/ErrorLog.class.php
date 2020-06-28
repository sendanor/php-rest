<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\Log;

/**
 * Logger which logs to process standard error
 *
 * @package SimpleREST\Log
 */
class ErrorLog extends BaseLogger {

  /**
   * @param $msg string
   */
  protected static function _write ($level, $msg) {

    fwrite(STDERR, '[' . $level . '] ' . $msg );

  }

  /**
   * @param string $msg
   */
  public function error ($msg) {
    self::_write('ERROR', $msg );
  }

  /**
   * @param string $msg
   */
  public function info ($msg) {
    self::_write('INFO', $msg );
  }

  /**
   * @param string $msg
   */
  public function warning ( $msg ) {
    self::_write('WARN', $msg );
  }

}
