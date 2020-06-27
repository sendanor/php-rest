<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko-Heikki Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\Logger;

class Syslog {

  public function __construct ($name) {

    openlog($name, LOG_PID | LOG_PERROR, LOG_LOCAL0);

  }

  public function err ($msg) {

    syslog(LOG_ERR, $msg);

  }

  public function info ($msg) {

    syslog(LOG_INFO, $msg);

  }

}
