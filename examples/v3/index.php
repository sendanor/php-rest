<?php

require_once( dirname(__FILE__) . '/config.php');

require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/index.php' );

use SimpleREST\Request as Request;
use SimpleREST\Response as Response;
use SimpleREST\Log\Log as Log;

require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/File/index.php' );

use SimpleREST\File\JSON as JSON;
use SimpleREST\File\EditableJSON as EditableJSON;

require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/Mail/index.php' );

use SimpleREST\Mail\Mailer as Mailer;
use SimpleREST\Mail\Message as Message;

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

/**
 * Class MyAPIRequest
 */
class MyAPI {

  /**
   * @Route /hello
   * @noinspection PhpUnused
   * @throws Exception if headers already sent
   */
  static public function hello () {

    Log::debug('--- Matched /hello ---');

    Request::run(MyHelloRequest::class );

    # Other for /hello
    Log::debug('--- Matched /hello with other method ---');
    Response::outputError(405);

  }

  /**
   * Change API data completely
   *
   * @Route put /
   * @noinspection PhpUnused
   */
  static public function update () {

    $METHOD = Request::getMethod();
    $PATH = Request::getPath();

    Log::debug('--- Matched / with PUT method ---');

    $DATA = new EditableJSON(REST_DATA_FILE);

    $DATA->setValue(Request::getInput());

    Log::info("$METHOD $PATH: Changed resource completely");

    return $DATA;

  }

  /**
   * Fetch complete API data
   *
   * @Route get /
   * @Route head /
   * @noinspection PhpUnused
   */
  static public function fetch () {

    Log::debug('--- Matched / with GET or HEAD method ---');

    return new JSON(REST_DATA_FILE);

  }

  /**
   * Testing single parameter syntax
   *
   * @Route( get /ping/? )
   * @param $param1
   * @return mixed
   * @noinspection PhpUnused
   */
  static public function ping ($param1) {

    Log::debug('--- Matched /ping/? with GET method ---');
    return $param1;

  }

  /**
   * Testing two single parameters syntax
   *
   * @Route( get /ping/?/? )
   * @param $param1
   * @param $param2
   * @return mixed
   * @noinspection PhpUnused
   */
  static public function ping2 ($param1, $param2) {

    Log::debug('--- Matched /ping/?/? with GET method ---');
    return [$param1, $param2];

  }

  /**
   * @param $param1
   * @param $param2
   * @param $obj
   * @return array
   * @Route get /ping/?/?/:foo
   * @Route get /ping/?/?/:foo/:bar
   * @Route get /ping/?/?/:foo/:bar/*
   * @noinspection PhpUnused
   */
  static public function ping3 ($param1, $param2, $obj) {

    Log::debug('--- Matched /ping/?/?/... with GET method ---');

    return [$param1, $param2, $obj];

  }

  /**
   * @Route post /sendMail
   * @return array
   * @throws \SimpleREST\Mail\MailError if cannot send email
   * @noinspection PhpUnused
   */
  static public function sendMail () {

    $msg = Request::getInput();

    return Mailer::send( new Message(
      $msg['to'],
      $msg['subject'],
      $msg['body'] ?? '',
      $msg['headers'] ?? array()
    ) );

  }

  /**
   * Other methods for / only
   *
   * @Route * /
   * @noinspection PhpUnused
   * @throws Exception if headers already sent
   */
  static public function methodNotFoundError () {

    Log::debug('--- Matched / with other method ---');

    Response::outputError(405);

  }

  /**
   * Any other path
   *
   * @throws Exception
   * @noinspection PhpUnused
   */
  static public function notFoundError () {

    Log::debug('--- Unknown request ---');
    Response::outputError(404);

  }

}

Request::run( function () {

  Request::run(MyAPI::class);

  MyAPI::notFoundError();

});
