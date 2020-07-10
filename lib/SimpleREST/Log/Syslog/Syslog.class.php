<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */
declare(strict_types=1);

namespace SimpleREST\Log;

/**
 * Class Syslog
 * @package SimpleREST\Log
 */
class Syslog extends BaseLogger {

  /**
   * Syslog constructor.
   *
   * @param $name string
   */
  public function __construct ($name) {

    openlog($name, LOG_PID | LOG_PERROR, LOG_LOCAL0);

  }

  /**
   * @param int $level The log level for syslog()
   * @param string[] $values Log message values to print
   */
  protected function _write ($level, array $values) {

    syslog($level, stringifyValues(...$values));

  }

  /**
   * @param string[] $msg
   */
  public function error (...$msg) {

    $this->_write(LOG_ERR, $msg);

  }

  /**
   * @param string[] $msg
   */
  public function info (...$msg) {

    $this->_write(LOG_INFO, $msg);

  }

  /**
   * @param string[] $msg
   */
  public function debug (...$msg) {

    $this->_write(LOG_DEBUG, $msg);

  }

  /**
   * @param string[] $msg
   */
  public function warning ( ...$msg ) {

    $this->_write(LOG_WARNING, $msg);

  }

}
