<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\File;

use Exception;
use JsonSerializable;

/**
 * Implements readable accessor for text files in the filesystem.
 *
 */
class Text implements JsonSerializable {

  /**
   * @var string|null
   */
  private $_file;

  /**
   * @var ReadLock|null
   */
  private $_readLock = null;

  /**
   * @var mixed|null
   */
  private $_data;

  /**
   * Text file reader constructor.
   *
   * @param $file string
   * @throws Exception if cannot open lock file or file handle not found
   */
  public function __construct ($file) {

    $this->_file = $file;

    $this->_initLock();

    $this->_data = $this->_loadData();

  }

  /**
   * Automatically releases the lock
   *
   * @throws Exception
   */
  public function __destruct () {

    $lock = $this->_getLock();

    if ( $lock !== null ) {
      $lock->release();
    }

  }

  /**
   * @return string
   */
  protected function _getLockFileName () {
    return $this->_getFileName() . ".lock";
  }

  /**
   * @return string
   */
  protected function _getFileName () {
      return $this->_file;
  }

  /**
   * @return array|string|null
   */
  protected function _getData () {
    return $this->_data;
  }

  /**
   * @param $value array|string|null
   */
  protected function _setData ($value) {
    $this->_data = $value;
  }

  /**
   * @param $property string
   * @param $value
   */
  protected function _setDataProperty ($property, $value) {
    $this->_data[$property] = $value;
  }

  /**
   * @param $property string
   */
  protected function _unsetDataProperty ($property) {
    unset($this->_data[$property]);
  }

  /**
   * @return ReadLock|null
   */
  protected function _getLock () {

    return $this->_readLock;

  }

  protected function _loadData () {

    // We must clear cache for file_exists
    clearstatcache();

    $file = $this->_getFileName();

    return file_exists($file) ? file_get_contents($file) : array();

  }

  /**
   * @throws Exception if cannot open lock file or file handle not found
   */
  protected function _initLock () {

    $this->_readLock = new ReadLock( $this->_getLockFileName() );

  }

  /**
   * @return string
   */
  public function __toString () {

    return "" . $this->_getData();

  }

  public function jsonSerialize () {

    return $this->_getData();

  }

  /**
   * @return mixed
   */
  public function getValue () {

    return $this->_getData();

  }

}
