<?php

namespace SimpleREST\Database;

/**
 * Interface iQuery
 */
interface iQuery {

  /**
   * Bind params
   *
   * @param array $params
   */
  public function bind (array $params = array());

  /**
   * @param array|null $params
   */
  public function execute ($params = null);

  /**
   * @return void
   */
  public function close ();

}
