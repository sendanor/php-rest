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
class EditableText extends Text {

  /**
   * @var WriteLock|null
   */
  private $_writeLock = null;

  /**
   * @var bool
   */
  private $_changed = false;

  /**
   * EditableText constructor.
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

  protected function _save () {

    if ($this->_changed) {
      file_put_contents( $this->_getFileName(), $this->_getData() );
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

  public function setValue ($data) {

    $this->_setData($data);

  }

}

