<?php

class OutputBuffer {

  public function __construct ($fn) {

    ob_start($fn);

  }

  public function __destruct () {


  }

}