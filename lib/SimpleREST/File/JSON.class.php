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
class JSON extends BaseJSON {

  private $read_lock = null;
  protected $data = null;
  protected $file = null;

  /** Create a lock file */
  public function __construct ( $file ) {

    parent::__construct();

    $this->file = $file;

    $this->initLock( $file . ".lock" );

    // We must clear cache for file_exists
    clearstatcache();

    $this->data = file_exists($file) ? json_decode(file_get_contents($file), true) : array();

  }

  /** Automatically releases the lock */
  public function __destruct () {

    if ( $this->getLock() !== null ) {
      $this->getLock()->release();
    }

    parent::__destruct();

  }

  protected function getLock () {

    return $this->read_lock;

  }

  protected function initLock ($file) {

    $this->read_lock = new ReadLock( $file );

  }

  public function getInternal () {
    return $this->data;
  }

  public function __isset ($property) {

    return isset($this->data[$property]);

  }

  public function __get ($property) {

    if ( isset($this->data[$property]) ) {
      return $this->data[$property];
    }

  }

}

