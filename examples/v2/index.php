<?php

require_once( dirname(__FILE__) . '/config.php');

require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/index.php' );

use SimpleREST\Request as Request;
use SimpleREST\Response as Response;
use SimpleREST\Log\Log as Log;

require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/File/index.php' );

use SimpleREST\File\JSON as JSON;
use SimpleREST\File\EditableJSON as EditableJSON;

Request::run(function () {

  $METHOD = Request::getMethod();
  $PATH = Request::getPath();
  Log::debug("$METHOD $PATH: Request started");

  # /hello
  Request::match("/hello", function () use ($METHOD, $PATH) {

    Log::debug('--- Matched /hello ---');

    # PUT /hello
    Request::match("put", function() use ($METHOD, $PATH) {

      Log::debug('Matched /hello with PUT');

      $DATA = new EditableJSON( REST_DATA_FILE );

      /** @noinspection PhpUndefinedFieldInspection */
      $DATA->hello = Request::getInput();

      Log::info("$METHOD $PATH: Changed hello property as '" . $DATA->hello . "'");

      return $DATA->hello;

    });

    # GET|HEAD /hello
    Request::match(["get", "head"], function() {

      Log::debug('--- Matched /hello with GET or HEAD ---');

      $DATA = new JSON( REST_DATA_FILE );

      return isset($DATA->hello) ? $DATA->hello : null;

    });

    Log::debug('--- Matched /hello with other method ---');

    # Other for /hello
    Response::outputError(405);

  });


  # PUT /
  Request::match("put /", function () use ($METHOD, $PATH) {

    Log::debug('--- Matched / with PUT method ---');

    $DATA = new EditableJSON(REST_DATA_FILE);

    $DATA->setValue(Request::getInput());

    Log::info("$METHOD $PATH: Changed resource completely");

    return $DATA;

  });

  # HEAD|GET /
  Request::match(["get /", "head /"], function () {
    Log::debug('--- Matched / with GET or HEAD method ---');
    return new JSON(REST_DATA_FILE);
  });

  # GET /ping
  Request::match("get /ping/?", function ($param1) {
    Log::debug('--- Matched /ping/? with GET method ---');
    return $param1;
  });

  # GET /ping
  Request::match("get /ping/?/?", function ($param1, $param2) {
    Log::debug('--- Matched /ping/?/? with GET method ---');
    return [$param1, $param2];
  });

  # GET /ping
  Request::match("get /ping/?/?/:foo", function ($param1, $param2, $obj) {
    Log::debug('--- Matched /ping/?/? with GET method ---');
    return [$param1, $param2, $obj];
  });

  # GET /ping
  Request::match("get /ping/?/?/:foo/:bar", function ($param1, $param2, $obj) {
    Log::debug('--- Matched /ping/?/? with GET method ---');
    return [$param1, $param2, $obj];
  });

  # GET /ping
  Request::match("get /ping/?/?/:foo/:bar/*", function ($param1, $param2, $obj) {
    Log::debug('--- Matched /ping/?/?/:foo/:bar/* with GET method ---');
    return [$param1, $param2, $obj];
  });

  # Other methods for /
  Request::match("* /", function () {
    Log::debug('--- Matched / with other method ---');
    Response::outputError(405);
  });

  # Any other path...
  Log::debug('--- Unknown request ---');
  Response::outputError(404);

});
