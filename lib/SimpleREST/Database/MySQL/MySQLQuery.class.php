<?php
declare(strict_types=1);

namespace SimpleREST\Database;

require_once( dirname(dirname(__FILE__)) . '/iQuery.interface.php');
require_once( dirname(dirname(dirname(__FILE__))) . '/Log/index.php');

use mysqli;
use mysqli_stmt;
use Exception;
use TypeError;
use SimpleREST\Log\Log;

/**
 * Class MySQLQuery
 *
 * Users should not use these classes directly.
 *
 *     1. Get a iConnection instance using `SimpleREST\Database\Connection::create($config)`
 *     2. Call `connection.createQuery($query)` or `connection.query($query[, $params])`
 *
 * @package SimpleREST\Database
 */
class MySQLQuery implements iQuery {

  /**
   * The prepared statement
   *
   * @var mysqli_stmt|null
   */
  private $_st;

  /**
   * @var null
   */
  private $_params;

  /**
   * MySQLQuery constructor.
   *
   * Users should not use these constructors directly.
   *
   *     1. Get a iConnection instance using `SimpleREST\Database\Connection::create($config)`
   *     2. Call `connection.createQuery($query)` or `connection.query($query[, $params])`
   *
   * @param string $query
   * @param mysqli $mysqli
   * @throws Exception if fails to prepare the query
   */
  public function __construct ( string $query, $mysqli ) {

    Log::debug('QUERY = ', $query);

    $st = $mysqli->prepare($query);

    if ($st === FALSE) {
      throw new Exception('Failed to prepare MySQL query: ' . $mysqli->error . ' (' . $mysqli->errno .')');
    }

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

    $this->_bindParams($params);

  }

  /**
   * Execute prepared query
   * @param array|null $params Optional parameters
   * @return array
   * @throws Exception if query had been closed already or binding failed in result params
   */
  public function execute ($params = null) {

    if ($this->_st === NULL ) {
      throw new Exception('Query has been closed!');
    }

    if ( $params !== NULL ) {
      $this->bind($params);
    }

    Log::debug('Executing with params: ', $this->_params);

    if ( $this->_st->execute() === FALSE ) {
      throw new Exception('Failed to execute query: ' . $this->_st->error . '(' . $this->_st->errno . ')');
    }

//    Log::debug( 'We got ' . $this->_st->num_rows . ' rows.');
//    return $this->_st->num_rows > 1 ? $this->_fetchResults() : array();

    return $this->_fetchResults();

  }

  /**
   * @param array $params
   * @throws Exception if parameter binding fails
   * @throws TypeError if param type is unsupported
   */
  protected function _bindParams (&$params) {

    $types = "";

    foreach ($params as &$param) {
      $types .= $this->_getBindParamTypeString($param);
    }

    if ( $this->_st->bind_param($types, ...$params) === FALSE ) {
      throw new Exception('Failed to bind params: ' . $this->_st->error . '(' . $this->_st->errno . ')');
    }

  }

  /**
   * Returns the type character for mysqli_stmt bind_param
   *
   * @param mixed $param
   * @return string
   * @throws TypeError if param type is unsupported
   */
  protected function _getBindParamTypeString (&$param) {

    if ( is_integer($param) ) return "i";
    if ( is_bool($param)    ) return "i";
    if ( is_double($param)  ) return "d";
    if ( is_string($param)  ) return "s";
    //if ( is_string($param)  ) return "b";

    throw new TypeError('Unsupported variable type: ' . var_export($param, true));

  }

  /**
   * @return array
   * @throws Exception if fails to bind result parameters
   */
  protected function _fetchResults () {

    $meta = $this->_st->result_metadata();

    if ($meta === FALSE) {

      if ($this->_st->error) {
        throw new Exception('Failed to fetch meta data: ' . $this->_st->error . '(' . $this->_st->errno . ')');
      }

      // This statement did not return a result set
      return array();
    }

    $keys = array();
    $values = array();
    try {

      while ( $field = $meta->fetch_field() ) {
        $keys[] = $field->name;
        $values[] = null;
      }

    } finally {
      $meta->close();
    }

    if (count($values) >= 1) {

      Log::debug('Binding result values (' . count($values) . ')');

      if ( $this->_st->bind_result(...$values) === FALSE ) {
        throw new Exception('Failed to bind results: ' . $this->_st->error . '(' . $this->_st->errno . ')');
      }

    }

    $results = array();

    while ( $this->_st->fetch() ) {
      $results[] = array_combine($keys, $values);
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
      $this->_st->close();
    }

    $this->_st = null;

  }

}
