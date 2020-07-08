<?php
/*
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko-Heikki Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\File;

use SimpleREST\JSON as JSONFormat;

/**
 * Implements lock file implementation
 *
 */
class JSON extends Text {

  public function __construct ( $file ) {

    parent::__construct($file);

  }

  /** Automatically releases the lock */
  public function __destruct () {

    parent::__destruct();

  }

  public function __toString () {

    return JSONFormat::encode( $this->_getData() );

  }

  public function __isset ($property) {

    $data = $this->_getData();

    return isset($data[$property]);

  }

  public function __get ($property) {

    $data = $this->_getData();

    if ( isset($data[$property]) ) {
      return $data[$property];
    }

    return null;

  }

  protected function _loadData () {

    $dataString = parent::_loadData();

    if ($dataString === NULL) {
      return array();
    }

    return JSONFormat::decode( $dataString );

  }

}

