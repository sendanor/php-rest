<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\Log;

/**
 * Class Logger
 * @package SimpleREST\Logger
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

}
