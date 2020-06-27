<?php
/*
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko-Heikki Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\File;

use Exception;

/**
 * Implements lock file implementation
 *
 */
class EditableJSON extends JSON {

  private $write_lock = null;
  private $changed = false;

  /** Create a lock file */
  public function __construct ( $file ) {

    parent::__construct( $file );

  }

  public function __destruct () {

    if ($this->changed) {
      file_put_contents( $this->file, json_encode($this->data) . "\n" );
    }

    parent::__destruct();

  }

  protected function getLock () {

    return $this->write_lock;

  }

  protected function initLock ($file) {

    $this->write_lock = new WriteLock( $file );

  }

  public function setInternal ($data) {

    $this->data = $data;

    $this->changed = true;

  }

  public function __unset ($property) {

    $this->changed = true;
    unset($this->data[$property]);

  }

  public function __set ($property, $value) {

    $this->changed = true;

    $this->data[$property] = $value;

  }

}

