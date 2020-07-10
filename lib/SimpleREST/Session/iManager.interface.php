<?php
declare(strict_types=1);

namespace SimpleREST\Session;

interface iManager {

  /**
   * Fetch a session by key
   *
   * @param string $key
   * @return Session
   */
  public function getSession (string $key);

  /**
   * Create a new session by key
   *
   * @param string $key
   * @return Session
   */
  public function createSession (string $key);

}
