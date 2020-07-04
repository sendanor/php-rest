<?php

namespace SimpleREST\Session;

use SimpleREST\Request;
use SimpleREST\Response;
use SimpleREST\Random;
use Exception;

if (!defined('REST_SESSION_HEADER')) {
  define('REST_SESSION_HEADER', 'SessionId');
}

class Manager {

  /**
   * @var iManager
   */
  private static $_manager = null;

  /**
   * Create a new (random) session key.
   *
   * @return string
   * @throws Exception if an appropriate source of randomness cannot be found
   */
  public static function createSessionKey () {

    return Random::string(32);

  }

  /**
   * @return Session
   * @throws Exception if session must be created but an appropriate source of randomness cannot be found
   */
  public static function createSession () {

    self::initManager();

    $sessionKey = self::createSessionKey();

    $session = self::$_manager->createSession($sessionKey);

    Response::setHeader(REST_SESSION_HEADER, $sessionKey);

    return $session;

  }

  /**
   * @return Session
   * @throws Exception if session header could not be found
   */
  public static function getSession () {

    self::initManager();

    $sessionKey = Request::getHeader(REST_SESSION_HEADER);

    if ($sessionKey === null) {
      throw new Exception('Could not find a session header');
    }

    return self::$_manager->getSession($sessionKey);

  }

  /**
   * Initializes default session manager
   */
  public static function initManager () {

    if ( self::$_manager === null ) {

      require_once(dirname(__FILE__).'/Database/index.php');

      self::$_manager = new Database\Manager();

    }

  }

}