<?php

// Import our core library
require( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/index.php' );

// Import our JSON file library
require( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/File/index.php' );

use SimpleREST\Logger\Syslog as Logger;
$logger = new Logger("myScriptLog");

use SimpleREST\File\JSON as JSON;
use SimpleREST\File\EditableJSON as EditableJSON;

use SimpleREST\Request as Request;
use SimpleREST\Response as Response;

define('DATA_FILE', dirname(__FILE__) . "/data.json");

$METHOD = Request::getMethod();
$PATH   = Request::getPath();

$logger->info("Request '$METHOD' '$PATH'");

switch ( $PATH ) {

# hello path...
case "/hello":

  switch ( $METHOD ) {

  # PUT /hello
  case "put":
    $DATA = new EditableJSON( DATA_FILE );
    $DATA->hello = Request::getInput();
    Response::outputJSON( $DATA->hello );
    break;

  # GET|HEAD /hello
  case "head":
  case "get":
    $DATA = new JSON( DATA_FILE );
    Response::outputJSON( $DATA->hello );
    break;

  # Other...
  default:
    Response::outputError(405);
    break;
  }
  break;


# Root path...
case "/":

  switch ( $METHOD ) {

  # PUT /
  case "put":
    $DATA = new EditableJSON( DATA_FILE );
    $DATA->setInternal( Request::getInput() );
    Response::output( $DATA );
    break;

  # GET /
  case "head":
  case "get":
    $DATA = new JSON( DATA_FILE );
    Response::output( $DATA );
    break;

  # Other methods
  default:
    Response::outputError(405);
  }
  break;


# Any other path...
default:

  Response::outputError(404);

}
