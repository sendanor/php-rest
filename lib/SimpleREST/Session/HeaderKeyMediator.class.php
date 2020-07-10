<?php
declare(strict_types=1);

namespace SimpleREST\Session;

require_once( dirname(dirname(__FILE__)) .'/Request.class.php' );
require_once( dirname(dirname(__FILE__)) .'/Response.class.php' );

require_once(dirname(__FILE__).'/iKeyGenerator.interface.php');

use SimpleREST\Request;
use SimpleREST\Response;
use Exception;

/**
 * Class HeaderKeyMediator
 *
 * HTTP header based session key mediator.
 *
 * @package SimpleREST\Session
 */
class HeaderKeyMediator implements iKeyMediator {

  /**
   * @var string
   */
  private $_headerKey;

  /**
   * HeaderKeyMediator constructor.
   *
   * @param string $headerKey
   */
  public function __construct ( string $headerKey ) {

    $this->_headerKey = $headerKey;

  }

  /**
   * @return string
   * @throws Exception
   */
  public function getKey () : string {

    $key = Request::getHeader($this->_headerKey);

    if ($key === null) {
      throw new Exception('Could not find a session header');
    }

    return $key;

  }

  /**
   * @param string $key
   */
  public function setKey ( string $key ) : void {

    Response::setHeader($this->_headerKey, $key);

  }

  /**
   * @return bool
   * @throws Exception
   */
  public function hasKey () : bool {

    return Request::hasHeader($this->_headerKey);

  }

}
