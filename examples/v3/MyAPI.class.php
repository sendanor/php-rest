<?php

/** @noinspection SqlNoDataSourceInspection */
/** @noinspection SqlDialectInspection */

require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/File/index.php' );
require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/File/JSON/index.php' );
require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/File/PHP/index.php' );
require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/Mail/index.php' );
require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/Database/index.php' );
require_once( dirname(__FILE__) . '/MyHelloRequest.class.php' );

use SimpleREST\Request as Request;
use SimpleREST\Response as Response;

use SimpleREST\Log\Log;
use SimpleREST\Database;
use SimpleREST\HTTPError;

use SimpleREST\File\PHP as PHPTemplate;
use SimpleREST\File\JSON;
use SimpleREST\File\EditableJSON ;

use SimpleREST\Mail\MailError;
use SimpleREST\Mail\Mailer;
use SimpleREST\Mail\Message;
use SimpleREST\Mail\SentMessage;

if (!defined('SELECT_REG_QUERY')) {
  define('SELECT_REG_QUERY', /** @lang text */ 'SELECT * FROM reg');
}

if (!defined('INSERT_REG_QUERY')) {
  define('INSERT_REG_QUERY', /** @lang text */ 'INSERT INTO reg (reg_email) VALUES (?)');
}

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

  /**
   * Database select example
   *
   * @Route get /reg
   * @Route head /reg
   * @noinspection PhpUnused
   * @throws Exception if binding failed in result params
   */
  static public function fetchReg () {

    Log::debug('--- Matched /reg with GET or HEAD method ---');

    $db = Database\Connection::create();

    $rows = $db->query(SELECT_REG_QUERY);

    return array(
      "ok" => true,
      "type" => "reg:list",
      "payload" => $rows
    );

  }

  /**
   * Database insert example
   *
   * @Route post /reg
   * @noinspection PhpUnused
   * @throws Exception if binding failed in result params
   * @throws HTTPError with 422 if email address is incorrect in input
   * @throws HTTPError with 409 if email address is already in the database
   */
  static public function insertReg () {

    Log::debug('--- Matched /reg with GET or HEAD method ---');

    $input = Request::getInput() ?? null;

    $email = $input['email'] ?? null;

    if (!SimpleREST\Validate::isEmail($email)) {
      throw new HTTPError(422, "email param is incorrect");
    }

    $db = Database\Connection::create();

    try {

       $db->query(INSERT_REG_QUERY, array($email));

       $id = $db->getLastInsertID();

       return array(
         "ok" => true,
         "id" => $id
       );

    } catch (Exception $e) {

      if ( stripos($e->getMessage(), "Duplicate entry") !== FALSE ) {
        Log::error("Original exception: " . $e);
        throw new HTTPError(409);
      }

      throw $e;

    }

  }

}
