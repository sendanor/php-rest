<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko-Heikki Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\File;

/**
 * Implements read/write accessor for JSON data on a file in the filesystem.
 *
 */
class JSON implements \JsonSerializable {

  private $changed = false;
  private $file = null;
  private $data = null;
  private $lock = null;

  /**
   * Note! If you disable using lock file, this implementation might not be production ready.
   */
  public function __construct ( $file, $use_lock = true ) {

    $this->file = $file;

    $this->lock = $use_lock === true ? new WriteLock( $file . ".lock" ) : null;

    $this->data = file_exists($file) ? json_decode(file_get_contents($file), true) : array();

  }

  public function setInternal ($data) {

    $this->data = $data;

    $this->changed = true;

  }

  public function __isset ($property) {

    return isset($this->data[$property]);

  }

  public function __unset ($property) {

    $this->changed = true;
    unset($this->data[$property]);

  }

  public function __get ($property) {

    if ( isset($this->data[$property]) ) {
      return $this->data[$property];
    }

  }

  public function __set ($property, $value) {

    $this->changed = true;

    $this->data[$property] = $value;

  }

  public function __destruct () {

    if ($this->changed) {
      file_put_contents( $this->file, json_encode($this->data) . "\n" );
    }

    if ( $this->lock !== null ) {
      $this->lock->release();
    }

  }

  public function __toString () {

    return json_encode($this->data);

  }

  public function jsonSerialize () {

    return $this->data;

  }

}
