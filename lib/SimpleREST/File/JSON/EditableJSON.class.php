<?php
/*
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\File;

use Exception;

/**
 * Implements lock file implementation
 *
 */
class EditableJSON extends JSON {

  /**
   * @var WriteLock|null
   */
  private $_writeLock = null;

  /**
   * @var bool
   */
  private $_changed = false;


  /**
   * EditableJSON constructor.
   *
   * @param $file string
   */
  public function __construct ( $file ) {

    parent::__construct( $file );

  }

  public function __destruct () {

    $this->_save();

    parent::__destruct();

  }

  /**
   * @param $data mixed
   */
  public function setValue ($data) {

    $this->_setData( $data );

  }

  /**
   * @param $property string
   */
  public function __unset ($property) {

    $this->_unsetDataProperty($property);

  }

  /**
   * @param $property string
   * @param $value mixed
   */
  public function __set ($property, $value) {

    $this->_setDataProperty($property, $value);

  }


  protected function _save () {

    if ($this->_changed) {
      file_put_contents( $this->_getFileName(), json_encode($this->_getData()) . "\n" );
    }

  }

  protected function _getLock () {

    return $this->_writeLock;

  }

  /**
   * @throws Exception
   */
  protected function _initLock () {

    $this->_writeLock = new WriteLock( $this->_getLockFileName() );

  }

  protected function _setData ($value) {

    parent::_setData($value);

    $this->_changed = true;

  }

  /**
   * @param $property string
   * @param $value
   */
  protected function _setDataProperty ($property, $value) {

    parent::_setDataProperty($property, $value);

    $this->_changed = true;

  }

  /**
   * @param $property string
   */
  protected function _unsetDataProperty ($property) {

    parent::_unsetDataProperty($property);

    $this->_changed = true;

  }

}

