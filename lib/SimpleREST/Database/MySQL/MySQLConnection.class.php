<?php
declare(strict_types=1);

namespace SimpleREST\Database;

use mysqli;
use Exception;
use SimpleREST\Log\Log;
use SimpleREST\Assert;

/**
 * Class MySQLConnection
 *
 * Users should not use these classes directly.
 *
 *     1. Get a iConnection instance using `SimpleREST\Database\Connection::create($config)`
 *     2. Call `connection.createQuery($query)` or `connection.query($query[, $params])`
 *
 * @package SimpleREST\Database
 */
class MySQLConnection implements iConnection {

  /**
   * @var mysqli|null
   */
  private $_db;

  /**
   * MySQLConnection constructor.
   *
   * Users should not use these classes directly.
   *
   *     1. Get a iConnection instance using `SimpleREST\Database\Connection::create($config)`
   *     2. Call `connection.createQuery($query)` or `connection.query($query[, $params])`
   *
   * @param string|null $hostname
   * @param string|null $username
   * @param string|null $password
   * @param string|null $name
   * @param int|null $port
   * @param string|null $socket
   * @throws Exception if connection fails
   * @throws Exception if mysqli extension not loaded
   */
  public function __construct (
    $hostname = null,
    $username = null,
    $password = null,
    $name = null,
    $port = null,
    $socket = null
  ) {

    Log::debug('Connecting with', $hostname, $username, $password, $name, $port, $socket);

    Assert::extensionLoaded("mysqli");

    $db = new mysqli($hostname, $username, $password, $name, $port, $socket);

    if ( $db->connect_error ) {
      throw new Exception('Failed to connect to MySQL: ' . $db->connect_error . ' (' . $db->connect_errno .')');
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

    return new MySQLQuery($query, $this->_db);

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

    if ( $this->_db->close() === FALSE ) {

      Log::warning('Warning! Could not close the MySQL connection. We will assume it was closed.');

    }

    $this->_db = null;

  }

  /**
   * @return mixed
   */
  public function getLastInsertID () {
    return $this->_db->insert_id;
  }

}
