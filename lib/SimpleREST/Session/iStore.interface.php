<?php
declare(strict_types=1);

namespace SimpleREST\Session;

interface iStore {

  /**
   * Check if a session exists
   *
   * @param string $key
   * @return bool
   */
  public function hasSession (string $key) : bool;

  /**
   * Fetch a session by key
   *
   * @param string $key
   * @return Session
   */
  public function getSession (string $key) : Session;

  /**
   * Create a new session by key
   *
   * @param string $key
   * @return Session
   */
  public function createSession (string $key) : Session;

}
