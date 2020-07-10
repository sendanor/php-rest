<?php
declare(strict_types=1);

namespace SimpleREST\Session;

require_once(dirname(__FILE__).'/iManager.interface.php');
require_once(dirname(__FILE__).'/iStore.interface.php');
require_once(dirname(__FILE__).'/iKeyGenerator.interface.php');

use Exception;

class Manager implements iManager {

  /**
   * @var iStore
   */
  private $_store;

  /**
   * @var iKeyGenerator
   */
  private $_keyGenerator;

  /**
   * @var iKeyMediator
   */
  private $_keyMediator;

  /**
   * Manager constructor.
   *
   * @param iStore $store
   * @param iKeyGenerator $keyGenerator
   * @param iKeyMediator $keyMediator
   */
  public function __construct (
    iStore $store,
    iKeyGenerator $keyGenerator,
    iKeyMediator $keyMediator
  ) {

    $this->_store = $store;

    $this->_keyGenerator = $keyGenerator;

    $this->_keyMediator = $keyMediator;

  }

  /**
   * @return Session
   * @throws Exception if session must be created but an appropriate source of randomness cannot be found
   */
  public function createSession () : Session {

    $sessionKey = $this->_keyGenerator->createSessionKey();

    $session = $this->_store->createSession($sessionKey);

    $this->_keyMediator->setKey($sessionKey);

    return $session;

  }

  /**
   * @return Session
   * @throws Exception if session header could not be found
   */
  public function getSession () : Session {

    return $this->_store->getSession( $this->_keyMediator->getKey() );

  }

  /**
   * @return bool
   * @throws Exception
   */
  public function hasSession () : bool {

    if ( !$this->_keyMediator->hasKey() ) return false;

    return $this->_store->hasSession( $this->_keyMediator->getKey() );

  }

}