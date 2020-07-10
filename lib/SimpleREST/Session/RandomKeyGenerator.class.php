<?php
declare(strict_types=1);

namespace SimpleREST\Session;

require_once(dirname(__FILE__).'/iStore.interface.php');
require_once(dirname(__FILE__).'/iKeyGenerator.interface.php');

require_once( dirname(dirname(__FILE__)) . '/Random.class.php' );

use SimpleREST\Random;
use Exception;
use TypeError;

/**
 * Class RandomKeyGenerator
 *
 * Random key generator for sessions.
 *
 * @package SimpleREST\Session
 */
class RandomKeyGenerator implements iKeyGenerator {

  /**
   * @var int
   */
  private $_keySize;

  /**
   * RandomKeyGenerator constructor.
   *
   * @param int $keySize
   * @throws TypeError if $keySize is less than 1
   */
  public function __construct ( int $keySize = 32 ) {

    if ($keySize < 1) throw new TypeError('keySize must be at least 1');

    $this->_keySize = $keySize;

  }

  /**
   * @return string
   * @throws Exception
   */
  public function createSessionKey () : string {

    return Random::string($this->_keySize);

  }

}