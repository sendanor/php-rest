<?php

declare(strict_types=1);

namespace SimpleREST\Session;

require_once( dirname(__FILE__) . '/Session.class.php');

interface iManager {

  /**
   * Fetch a session
   *
   * @return Session
   */
  public function getSession () : Session;

  /**
   * Check if we have a session
   *
   * @return bool
   */
  public function hasSession () : bool;

  /**
   * Create a new session
   *
   * @return Session
   */
  public function createSession () : Session;

}
