<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */

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
   * @param string $msg
   */
  public function error ($msg) {

    syslog(LOG_ERR, $msg);

  }

  /**
   * @param string $msg
   */
  public function info ($msg) {

    syslog(LOG_INFO, $msg);

  }

  /**
   * @param string $msg
   */
  public function warning ( $msg ) {
    syslog(LOG_WARNING, $msg);
  }

}
