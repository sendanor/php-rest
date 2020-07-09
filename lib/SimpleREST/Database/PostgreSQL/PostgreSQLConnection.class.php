<?php

namespace SimpleREST\Database;

use Exception;
use SimpleREST\Log\Log;
use SimpleREST\Assert;

/**
 * Class PostgreSQLConnection
 *
 * Users should not use these classes directly.
 *
 *     1. Get a iConnection instance using `SimpleREST\Database\Connection::create($config)`
 *     2. Call `connection.createQuery($query)` or `connection.query($query[, $params])`
 *
 * @package SimpleREST\Database
 */
class PostgreSQLConnection implements iConnection {

  /**
   * @var resource|null
   */
  private $_db;

  /**
   * @var int
   */
  private $_prepared_query_name_index = 0;

  /**
   * PostgreSQLConnection constructor.
   *
   * Users should not use these classes directly.
   *
   *     1. Get a iConnection instance using `SimpleREST\Database\Connection::create($config)`
   *     2. Call `connection.createQuery($query)` or `connection.query($query[, $params])`
   *
   * @param string $connection_string
   * @param int $connect_type
   * @throws Exception if connection fails
   * @throws Exception if mysqli extension not loaded
   */
  public function __construct ( string $connection_string, int $connect_type = null ) {

    Log::debug('Connecting with', $connection_string, $connect_type);

    Assert::extensionLoaded("pg");

    if ($connect_type === null) {
      $db = @pg_connect($connection_string);
    } else {
      $db = @pg_connect($connection_string, $connect_type);
    }

    if ( $db === FALSE ) {
      throw new Exception('Failed to connect to PostgreSQL server');
    }

    $this->_db = $db;

  }

  function __destruct () {

    if ( $this->_db !== null ) {

      /** @noinspection PhpUnhandledExceptionInspection */
      $this->close();

    }

  }

  /**
   * Returns TRUE if connected to the database.
   *
   * @return bool
   */
  public function isConnected () {

    return $this->_db !== NULL;

  }

  /**
   * Create a prepared SQL query on the connection.
   *
   * @param string $query
   * @return iQuery
   * @throws Exception if fails to prepare the query
   */
  public function createQuery (string $query) {

    $this->_prepared_query_name_index += 1;

    $name = "pq" . $this->_prepared_query_name_index;

    return new PostgreSQLQuery($this->_db, $name, $query);

  }

  /**
   * Perform an SQL query on the connection.
   *
   * @param string $query
   * @param array|null $params
   * @return array
   * @throws Exception if binding failed in result params
   */
  public function query (string $query, array $params = null) {

    $q = $this->createQuery($query);

    return $q->execute($params);

  }

  /**
   * Close the connection
   *
   * @throws Exception if connection was already closed
   * @return void
   */
  public function close () {

    if ( $this->_db === null ) {
      throw new Exception('Connection was already closed!');
    }

    if ( @pg_close($this->_db) === FALSE ) {

      Log::warning('Warning! Could not close the PostgreSQL connection. We will assume it was closed.');

    }

    $this->_db = null;

  }

}
