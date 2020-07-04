<?php

namespace SimpleREST\Session;

use Exception;
use JsonSerializable;
use Serializable;
use SimpleREST\JSON;
use stdClass;

/**
 * Class BaseSession
 * @package SimpleREST\Database
 */
class Session implements JsonSerializable, Serializable {

  /**
   * @var string
   */
  private $_key;

  /**
   * @var array|null
   */
  private $_data;

  /**
   * @var bool
   */
  private $_changed;

  /**
   * BaseSession constructor.
   *
   * @param string $key
   * @param array|null $data
   * @param bool $changed
   * @throws Exception if $data is an empty array
   */
  public function __construct (string $key, $data = null, bool $changed = false) {

    if ( is_array($data) && count(array_keys($data)) === 0 ) {
      throw new Exception('Empty array not supported: use null instead.');
    }

    $this->_key = $key;

    $this->_data = $data;

    $this->_changed = $changed;

  }

  /**
   * Automatically saves the session data on destruction
   */
  public function __destruct () {

    $this->_data = null;
    $this->_changed = false;

  }

  /**
   * @return string
   */
  public function getKey () {
    return $this->_key;
  }

  /**
   * @return bool
   */
  public function isChanged () {
    return $this->_changed;
  }

  /**
   * @param string $key
   * @return mixed
   */
  protected function _getProperty (string $key) {

    if ($this->_data === null) return null;

    return $this->_data[$key];

  }

  /**
   * @param string $key
   * @return bool
   */
  protected function _issetProperty (string $key) {

    if ($this->_data === null) return false;

    return isset($this->_data[$key]);

  }

  /**
   * @param string $key
   * @param mixed $value
   */
  protected function _setProperty (string $key, $value) {

    if ($this->_data === null) {
      $this->_data = array();
    }

    $this->_data[$key] = $value;

    $this->_changed = true;

    $this->onChange();

  }

  /**
   * @param string $key
   */
  protected function _unsetProperty (string $key) {

    if ($this->_data === null) return;

    unset($this->_data[$key]);

    $this->_changed = true;

    $this->onChange();

  }

  /**
   * Called any time the data changes.
   *
   * This method is called automatically for most operations.
   *
   * @return void
   */
  public function onChange () {

  }

  /**
   * @param string $key
   */
  public function __get ($key) {
    $this->_getProperty($key);
  }

  /**
   * @param string $key
   * @param mixed $value
   */
  public function __set ($key, $value) {
    $this->_setProperty($key, $value);
  }

  /**
   * @param string $key
   * @return bool
   */
  public function __isset ($key) {
    return $this->_issetProperty($key);
  }

  /**
   * @param string $key
   */
  public function __unset ($key) {
    $this->_unsetProperty($key);
  }

  /**
   * @return array|stdClass
   */
  public function jsonSerialize () {

    if ($this->_data === null) {
      return new stdClass();
    }

    return $this->_data;

  }

  /**
   * @return string
   * @throws Exception if json encoding fails
   */
  public function serialize () {
    return JSON::encode($this->jsonSerialize());
  }

  /**
   * @param string $serialized
   * @return void
   */
  public function unserialize ($serialized) {

    $data = JSON::decode($serialized);

    if ( is_array($data) && count(array_keys($data)) === 0 ) {
      $data = null;
    }

    $this->_data = $data;

  }

  /**
   * @return string
   */
  public function __toString () {
    return JSON::encode($this->_data);
  }

}