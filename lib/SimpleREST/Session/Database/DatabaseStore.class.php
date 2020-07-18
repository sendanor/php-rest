<?php
declare(strict_types=1);

namespace SimpleREST\Session;

require_once( dirname(dirname(dirname(__FILE__))) . '/Database/Connection.class.php');
require_once( dirname(dirname(dirname(__FILE__))) . '/JSON.class.php');
require_once( dirname(dirname(dirname(__FILE__))) . '/Log/index.php');
require_once( dirname(dirname(dirname(__FILE__))) . '/Assert.class.php');

require_once( dirname(dirname(__FILE__)) . '/iStore.interface.php');

use SimpleREST\Database\Connection;
use SimpleREST\Database\iConnection;
use SimpleREST\Log\Log;
use SimpleREST\JSON;
use SimpleREST\Assert;
use Exception;
use TypeError;

if (!defined('FETCH_SESSION_SQL')) {
  define('FETCH_SESSION_SQL', 'SELECT * FROM `session` WHERE session_key = ? LIMIT 1');
}

if (!defined('CHECK_SESSION_SQL')) {
  define('CHECK_SESSION_SQL', 'SELECT COUNT(*) AS count FROM `session` WHERE session_key = ? LIMIT 1');
}

if (!defined('DELETE_SESSION_SQL')) {
  define('DELETE_SESSION_SQL', 'DELETE FROM `session` WHERE session_key = ? LIMIT 1');
}

if (!defined('CREATE_SESSION_SQL')) {
  define('CREATE_SESSION_SQL', 'INSERT INTO `session` (session_key, session_value) VALUES (?, ?)');
}

if (!defined('SAVE_SESSION_SQL')) {
  define('SAVE_SESSION_SQL', 'UPDATE `session` SET session_value = ? WHERE session_key = ? LIMIT 1');
}

/**
 * Class Manager
 *
 * @package SimpleREST\Session\Database
 */
class DatabaseStore implements iStore {

  /**
   * @var iConnection
   */
  private $_db;

  /**
   * Array of session objects
   *
   * @var array|null
   */
  private $_sessions;

  /**
   * DatabaseManager constructor.
   *
   * @param string|iConnection|null $config
   * @throws Exception if connection fails
   * @throws TypeError if database type is unsupported
   */
  public function __construct ( $config = null ) {

    if ($config instanceof iConnection) {
      $this->_db = $config;
    } else {
      $this->_db = Connection::create($config);
    }

    $this->_sessions = array();

  }

  /**
   * Saves session objects on
   */
  public function __destruct () {

    $this->close();

  }

  /**
   * Close (and save) any opened sessions
   */
  public function close () {

    if ($this->_sessions !== null) {

      foreach ($this->_sessions as $key => $value) {
        try {
          $this->_saveSession($value->getKey(), $value);
        } catch (Exception $e) {
          Log::error('ERROR: ' . $e);
        }
      }

      $this->_sessions = null;

    }

  }

  /**
   * @param string $key
   * @return Session
   * @throws Exception if session could not be found
   * @throws Exception if database errors
   */
  public function getSession ( string $key ) : Session {

    $rows = $this->_db->query(FETCH_SESSION_SQL, array($key));

    if (count($rows) === 0) {
      throw new Exception('Session not found');
    }

    $row = array_shift($rows);

    if ($row === null) {
      throw new Exception('Session not found');
    }

    $data = JSON::decode($row['session_value']);

    if ( is_array($data) && count(array_keys($data)) === 0 ) {
      $data = null;
    }

    $session = new Session($key, $data, false );

    $this->_sessions[] = $session;

    return $session;

  }

  /**
   * @param string $key
   * @return Session
   * @throws Exception if database errors
   */
  public function createSession ( string $key ) : Session {

    $session = new Session($key, null, false );

    $this->_db->query(CREATE_SESSION_SQL, array($key, JSON::encodeObject($session)));

    $this->_sessions[] = $session;

    return $session;

  }

  /**
   * @param string $key
   * @param Session $session
   * @throws Exception if database errors
   */
  protected function _saveSession (string $key, Session $session) {

    if ($session->isChanged()) {

      $data = JSON::encode($session);

      Assert::string($data);
      Assert::string($key);

      $this->_db->query(SAVE_SESSION_SQL, array($data, $key));

    }

  }

  /**
   * @param string $key
   * @return bool
   * @throws Exception
   */
  public function hasSession ( string $key ) : bool {

    $rows = $this->_db->query(CHECK_SESSION_SQL, array($key));

    if ( count($rows) === 0 ) return false;

    $row = array_shift($rows);

    if (!$row) return false;
    if (!isset($row['count'])) return false;

    return $row['count'] === 1;

  }

}