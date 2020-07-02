<?php

require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/File/index.php' );
require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/File/JSON/index.php' );
require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/File/PHP/index.php' );
require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/Mail/index.php' );
require_once( dirname(__FILE__) . '/MyHelloRequest.class.php' );

use SimpleREST\Request as Request;
use SimpleREST\Response as Response;

use SimpleREST\Log\Log as Log;

use SimpleREST\File\PHP as PHPTemplate;
use SimpleREST\File\JSON as JSON;
use SimpleREST\File\EditableJSON as EditableJSON;

use SimpleREST\Mail\MailError;
use SimpleREST\Mail\Mailer;
use SimpleREST\Mail\Message;
use SimpleREST\Mail\SentMessage;

/**
 * Class MyAPIRequest
 */
class MyAPI {

  /**
   * @var PHPTemplate|null
   */
  static protected $_email_template = null;

  /**
   */
  static protected function _initTemplate () {

    if (self::$_email_template === null) {
      self::$_email_template = new PHPTemplate( dirname(__FILE__) . '/template.php' );
    }

  }

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
   * @param Message $msg
   * @return string
   * @throws Exception if template function does not return internal scope object
   */
  static protected function _getMailTemplate ($msg) {

    self::_initTemplate();

    return self::$_email_template->execute( array('message' => $msg) );

  }

  /**
   * @Route post /getMailTemplate
   * @return string
   * @noinspection PhpUnused
   * @throws Exception if template function does not return internal scope object
   */
  static public function getMailTemplate () {

    $msg = Request::getInput();

    return self::_getMailTemplate($msg);

  }

  /**
   * @Route post /sendMail
   * @return SentMessage
   * @throws MailError if cannot send email
   * @noinspection PhpUnused
   */
  static public function sendMail () {

    self::_initTemplate();

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
