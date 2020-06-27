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
abstract class BaseJSON implements \JsonSerializable {

  abstract public function getInternal ();

  public function __construct () {}

  /** Automatically releases the lock */
  public function __destruct () {}

  public function __toString () {

    return json_encode($this->getInternal());

  }

  public function jsonSerialize () {

    return $this->getInternal();

  }

}
