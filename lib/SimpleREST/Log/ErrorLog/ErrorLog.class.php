<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\Log;

use Exception;
use TypeError;

/**
 * Logger which logs to process standard error
 *
 * @package SimpleREST\Log
 */
class ErrorLog extends BaseLogger {

  /**
   * @var false|resource|null
   */
  protected static $STDERR = null;

  /**
   * @var string|null
   */
  protected $_name = null;

  /**
   * Standard error logger constructor.
   *
   * @param string $name The name for logger
   * @throws Exception if cannot open standard error
   * @throws TypeError if $name is not a string
   */
  public function __construct ($name) {

    if (!is_string($name)) {
      throw new TypeError('Option was not string');
    }

    if ( self::$STDERR === null ) {
      $fp = fopen('php://stderr', 'w');
      if ($fp === FALSE) throw new Exception('Could not open STDERR for logging!');
      self::$STDERR = $fp;
    }

    $this->_name = $name;

  }

  /**
   * @param $level string
   * @param $values string[]
   */
  protected function _write ($level, array $values) {

    fwrite(self::$STDERR, '[' . $this->_name. '] [' . $level . '] ' . stringifyValues($values) . "\n" );

  }

  /**
   * @param mixed[] $msg
   */
  public function error (...$msg) {
    $this->_write('ERROR', $msg );
  }

  /**
   * @param mixed[] $msg
   */
  public function info (...$msg) {
    $this->_write('INFO', $msg );
  }

  /**
   * @param mixed[] $msg
   */
  public function warning ( ...$msg ) {
    $this->_write('WARN', $msg );
  }

  /**
   * @param mixed[] $msg
   */
  public function debug ( ...$msg ) {
    $this->_write('DEBUG', $msg );
  }

}
