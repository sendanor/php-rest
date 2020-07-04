<?php

namespace SimpleREST\Session;

use Exception;
use JsonSerializable;
use Serializable;
use SimpleREST\JSON;

/**
 * Class BaseSession
 * @package SimpleREST\Database
 */
class Session implements JsonSerializable, Serializable {

  /**
   * @var array
   */
  private $_data;

  /**
   * @var bool
   */
  private $_changed;

  /**
   * BaseSession constructor.
   *
   * @param array $data
   * @param bool $changed
   */
  public function __construct (array $data, bool $changed = false) {

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

    return $this->_data[$key];

  }

  /**
   * @param string $key
   * @return bool
   */
  protected function _issetProperty (string $key) {

    return isset($this->_data[$key]);

  }

  /**
   * @param string $key
   * @param mixed $value
   */
  protected function _setProperty (string $key, $value) {

    $this->_data[$key] = $value;

    $this->_changed = true;

    $this->onChange();

  }

  /**
   * @param string $key
   */
  protected function _unsetProperty (string $key) {

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
   * @return array
   */
  public function jsonSerialize () {
    return $this->_data;
  }

  /**
   * @return string
   * @throws Exception if json encoding fails
   */
  public function serialize () {
    return JSON::encode($this->_data);
  }

  /**
   * @param string $serialized
   * @return void
   */
  public function unserialize ($serialized) {

    $this->_data = JSON::decode($serialized);

  }

  /**
   * @return string
   */
  public function __toString () {
    return JSON::encode($this->_data);
  }

}