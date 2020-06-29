<?php

define('DATA_FILE', dirname(__FILE__) . "/data.json");

require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/index.php' );
require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/File/index.php' );
require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/Log/index.php' );
require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/Log/Syslog/index.php' );

use SimpleREST\File\JSON as JSON;
use SimpleREST\File\EditableJSON as EditableJSON;

use SimpleREST\Request as Request;
use SimpleREST\Response as Response;

Request::run(function () {

  SimpleREST\Log\setLogger( new SimpleREST\Log\Syslog("myScriptLog") );

  $METHOD = Request::getMethod();
  $PATH = Request::getPath();
  SimpleREST\Log\info("$METHOD $PATH: Request started");

  # /hello
  Request::match("/hello", function () use ($METHOD, $PATH) {

    # PUT /hello
    Request::match("put", function() use ($METHOD, $PATH) {

      $DATA = new EditableJSON( DATA_FILE );

      /** @noinspection PhpUndefinedFieldInspection */
      $DATA->hello = Request::getInput();

      SimpleREST\Log\info("$METHOD $PATH: Changed hello property as '" . $DATA->hello . "'");

      return $DATA->hello;

    });

    # GET|HEAD /hello
    Request::match(["get", "head"], function() {

      $DATA = new JSON( DATA_FILE );

      return isset($DATA->hello) ? $DATA->hello : null;

    });

    # Other for /hello
    Response::outputError(405);

  });


  # PUT /
  Request::match("put", "/", function () use ($METHOD, $PATH) {

    $DATA = new EditableJSON(DATA_FILE);

    $DATA->setValue(Request::getInput());

    SimpleREST\Log\info("$METHOD $PATH: Changed resource completely");

    return $DATA;

  });

  # HEAD|GET /
  Request::match(["get", "head"], "/", function () {
    return new JSON(DATA_FILE);
  });

  # Other methods for /
  Request::match("*", "/", function () {
    Response::outputError(405);
  });

  # Any other path...
  Response::outputError(404);

});
