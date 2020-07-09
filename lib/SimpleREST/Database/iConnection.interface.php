<?php

namespace SimpleREST\Database;

/**
 * Interface iConnection
 */
interface iConnection {

  /**
   * Returns TRUE if connected to the database.
   *
   * @return bool
   */
  public function isConnected ();

  /**
   * Create a prepared SQL query on the connection.
   *
   * @param string $query
   * @return iQuery
   */
  public function createQuery (string $query);

  /**
   * Perform an SQL query on the connection.
   *
   * @param string $query
   * @param array|null $params
   * @return array
   */
  public function query (string $query, array $params = null);

  /**
   * Close the connection
   *
   * @return void
   */
  public function close ();

}
