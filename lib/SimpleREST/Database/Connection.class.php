<?php
declare(strict_types=1);

namespace SimpleREST\Database;

use Exception;
use TypeError;

/**
 * Interface iDatabaseConnection
 */
class Connection {

  /**
   * Create a prepared SQL query on the connection.
   *
   * @param string $config Database connection string like `mysql://[username[:password]@][hostname][:port][/dbname]`
   * @return iConnection
   * @throws Exception if connection fails
   * @throws TypeError if database type is unsupported
   */
  static public function create ($config = null) {

    if ( $config === null && defined('REST_DATABASE') ) {
      $config = REST_DATABASE;
    }

    $url = $config !== null ? parse_url($config) : array();

    $type     = $url['scheme'] ?? null;

    switch ($type) {

      case "mysql":

        $hostname = $url['host'] ?? null;
        $username = $url['user'] ?? null;
        $password = $url['pass'] ?? null;
        $name     = isset($url['path']) ? trim( explode("/", $url['path'])[1] ) : null;
        $port     = $url['port'] ?? null;

        require_once( dirname(__FILE__) . "/MySQL/index.php" );
        return new MySQLConnection($hostname, $username, $password, $name, $port);

      case "psql":
      case "postgresql":
        require_once( dirname(__FILE__) . "/PostgreSQL/index.php" );
        return new PostgreSQLConnection($config, PGSQL_CONNECT_FORCE_NEW);

      default:
        throw new TypeError('Database type "' . $type . '" is unsupported.');

    }

  }

}
