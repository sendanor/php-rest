<?php
declare(strict_types=1);

namespace SimpleREST\Session;

require_once(dirname(__FILE__). '/iKeyMediator.interface.php');

use Exception;

/**
 * Class CookieKeyMediator
 *
 * HTTP header based session key mediator.
 *
 * @package SimpleREST\Session
 */
class CookieKeyMediator implements iKeyMediator {

  /**
   * @var string
   */
  private $_cookieName;

  /**
   * @var int
   */
  private $_expires;

  /**
   * @var string
   */
  private $_path;

  /**
   * @var string
   */
  private $_domain;

  /**
   * @var bool
   */
  private $_secure;

  /**
   * @var bool
   */
  private $_httpOnly;

  /**
   * CookieKeyMediator constructor.
   *
   * @param string $cookieName
   * @param int $expires
   * @param string $path
   * @param string $domain
   * @param bool $secure
   * @param bool $httpOnly
   */
  public function __construct (
    string $cookieName,
    int $expires = 0,
    string $path = '',
    string $domain = '',
    bool $secure = FALSE,
    bool $httpOnly = FALSE
  ) {

    $this->_cookieName = $cookieName;
    $this->_expires = $expires;
    $this->_path = $path;
    $this->_domain = $domain;
    $this->_secure = $secure;
    $this->_httpOnly = $httpOnly;

  }

  /**
   * @return string
   * @throws Exception if cookie could not be found
   * @throws Exception if cookie value was invalid
   */
  public function getKey () : string {

    if (!isset($_COOKIE[$this->_cookieName])) {
      throw new Exception('Could not find a cookie by name ' . $this->_cookieName);
    }

    $key = $_COOKIE[$this->_cookieName];

    if (!($key && is_string($key))) {
      throw new Exception('The cookie value was not a string ' . $this->_cookieName . ': ' . $key);
    }

    return $key;

  }

  /**
   * @param string $key
   * @throws Exception if cannot set the cookie (eg. output was sent already)
   */
  public function setKey ( string $key ) : void {

    if ( setcookie($this->_cookieName, $key, $this->_expires, $this->_path, $this->_domain, $this->_secure, $this->_httpOnly) === FALSE ) {
      throw new Exception('Could not set a cookie');
    }

  }

  /**
   * @return bool
   */
  public function hasKey () : bool {

    if (!isset($_COOKIE[$this->_cookieName])) return false;

    $key = $_COOKIE[$this->_cookieName];

    return !!($key && is_string($key));

  }

}
