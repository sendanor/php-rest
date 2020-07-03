<?php

namespace SimpleREST\Database;

/**
 * Interface iDatabaseConnection
 */
interface iQuery {

  /**
   * iDatabaseConnection constructor.
   *
   * @param string $query The query template
   * @param mixed $connection The internal connection object, or other internal options to make the query.
   */
  public function __construct (string $query, $connection);

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
