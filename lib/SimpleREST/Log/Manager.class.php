<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\Log;

use Exception;
use TypeError;

/**
 * Class Logger
 * @package SimpleREST\Log
 */
abstract class Manager {

  /**
   * @var BaseLogger|null
   */
  private static $logger = null;

  /**
   * @param $logger BaseLogger
   */
  public static function setLogger ($logger) {
    self::$logger = $logger;
  }

  /**
   * @return BaseLogger
   */
  public static function getLogger () {

    if (self::$logger === null) {
      self::$logger = new ErrorLog();
    }

    return self::$logger;

  }

  /**
   * @param string $name
   * @return BaseLogger|ErrorLog|Syslog
   * @throws Exception if $name is not a string
   * @throws TypeError if REST_LOGGER is defined and is not correct logger name
   */
  public static function createDefaultLogger ($name) {
    return createLogger( defined('REST_LOGGER') ? REST_LOGGER : 'stderr', $name);
  }

}
