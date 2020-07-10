<?php
declare(strict_types=1);

namespace SimpleREST\Database;

use Exception;
use SimpleREST\Log\Log;

/**
 * Class PostgreSQLQuery
 *
 * Users should not use these classes directly.
 *
 *     1. Get a iConnection instance using `SimpleREST\Database\Connection::create($config)`
 *     2. Call `connection.createQuery($query)` or `connection.query($query[, $params])`
 *
 * @package SimpleREST\Database
 */
class PostgreSQLQuery implements iQuery {

  /**
   * The prepared statement
   *
   * @var resource
   */
  private $_st;

  /**
   * The PostgreSQL connection resource
   *
   * @var resource
   */
  private $_connection;

  /**
   * @var string
   */
  private $_name;

  /**
   * @var array|null
   */
  private $_params;

  /**
   * PostgreSQLQuery constructor.
   *
   * Users should not use these constructors directly.
   *
   *     1. Get a iConnection instance using `SimpleREST\Database\Connection::create($config)`
   *     2. Call `connection.createQuery($query)` or `connection.query($query[, $params])`
   *
   * @param resource $connection
   * @param string $name
   * @param string $query
   * @throws Exception if fails to prepare the query
   */
  public function __construct ( $connection, string $name, string $query ) {

    Log::debug('QUERY = ', $query);

    $st = pg_prepare($connection, $name, $query);

    if ($st === FALSE) {
      throw new Exception('Failed to prepare PostgreSQL query: ' . pg_last_error($connection));
    }

    $this->_name = $name;

    $this->_connection = $connection;

    $this->_st = $st;

    $this->_params = null;

  }

  /**
   *
   */
  public function __destruct () {

    $this->close();

  }

  /**
   * Bind params
   *
   * @param array $params
   * @throws Exception if query had been closed already or $params is empty
   */
  public function bind (array $params = array()) {

    if ($this->_st === NULL ) {
      throw new Exception('Query has been closed!');
    }

    if (count($params) === 0) {
      throw new Exception('The params is empty!');
    }

    Log::debug('PARAMS = ', $params);

    $this->_params = $params;

  }

  /**
   * Execute prepared query
   *
   * @param array|null $params Optional parameters
   * @return array
   * @throws Exception if query had been closed already or binding failed in result params
   */
  public function execute ($params = null) {

    if ($this->_st === NULL ) {
      throw new Exception('Query has been closed!');
    }

    if ( $params === NULL ) {
      $params = $this->_params;
    }

    Log::debug('Executing with params: ', $params);

    $result = pg_execute($this->_connection, $this->_name, $params);

    if ( $result === FALSE ) {
      throw new Exception('Failed to execute query: ' . pg_last_error($this->_connection));
    }

    return $this->_fetchResults($result);

  }

  /**
   * @param $result
   * @return array
   * @throws Exception if fails to bind result parameters
   */
  protected function _fetchResults ($result) {

    $results = array();

    while ( $row = pg_fetch_array($result, NULL, PGSQL_ASSOC) ) {
      $results[] = $row;
    }

    Log::debug('Results fetched:', $results, '(' . count($results) . ')');

    return $results;

  }

  /**
   *
   */
  public function close () {

    $this->_params = null;

    if ($this->_st !== null) {
      pg_free_result($this->_st);
    }

    $this->_st = null;

  }

}
