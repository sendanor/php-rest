<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko-Heikki Heusala <jheusala@iki.fi>
 */

namespace REST2\File;

class JSON implements \JsonSerializable {

  private $changed = false;
  private $file = null;
  private $data = null;

  public function __construct ( $file ) {

    $this->file = $file;

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

  }

  public function __toString () {
    return json_encode($this->data);
  }

  public function jsonSerialize () {
    return $this->data;
  }

}
