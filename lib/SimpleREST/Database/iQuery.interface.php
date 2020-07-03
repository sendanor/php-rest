<?php

namespace SimpleREST\Database;

/**
 * Interface iQuery
 */
interface iQuery {

  /**
   * iQuery constructor.
   *
   * Users should not use these constructors directly.
   *
   *     1. Get a iConnection instance using `SimpleREST\Database\Connection::create($config)`
   *     2. Call `connection.createQuery($query)`
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
