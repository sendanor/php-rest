<?php

require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/File/index.php' );
require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/File/JSON/index.php' );

use SimpleREST\Request as Request;
use SimpleREST\Log\Log as Log;
use SimpleREST\File\JSON as JSON;
use SimpleREST\File\EditableJSON as EditableJSON;

/**
 * Class MyHelloRequest
 */
class MyHelloRequest {

  /**
   * @var string|null
   */
  private $_method;

  /**
   * @var string|null
   */
  private $_path;

  /**
   * MyHelloRequest constructor.
   *
   */
  public function __construct () {

    $this->_method = Request::getMethod();
    $this->_path = Request::getPath();

    Log::debug("{$this->_method} {$this->_path}: Hello request started");

  }

  /**
   * Replace hello resource completely
   *
   * @return string|null
   * @noinspection PhpUnused
   * @Route(put)
   */
  public function replace () {

    Log::debug('Matched /hello with PUT');

    $DATA = new EditableJSON( REST_DATA_FILE );

    /** @noinspection PhpUndefinedFieldInspection */
    $DATA->hello = Request::getInput();

    Log::info("{$this->_method} {$this->_path}: Changed hello property as '" . $DATA->hello . "'");

    return $DATA->hello;

  }

  /**
   * Fetch hello resource
   *
   * @return string|null
   * @Route get
   * @Route head
   * @noinspection PhpUnused
   */
  public function fetch () {

    Log::debug('--- Matched /hello with GET or HEAD ---');

    $DATA = new JSON( REST_DATA_FILE );

    return isset($DATA->hello) ? $DATA->hello : null;

  }

}
