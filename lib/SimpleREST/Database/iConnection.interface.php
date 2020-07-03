<?php

namespace SimpleREST\Database;

/**
 * Interface iDatabaseConnection
 */
interface iConnection {

  /**
   * iDatabaseConnection constructor.
   *
   * @param string|null $hostname
   * @param string|null $username
   * @param string|null $password
   * @param string|null $name
   * @param int|null $port
   * @param string|null $socket
   */
  public function __construct ($hostname = null, $username = null, $password = null, $name = null, $port = null, $socket = null);

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
