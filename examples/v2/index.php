<?php

// Import our core library
require( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/index.php' );

// Import our JSON file library
require( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/File/index.php' );

use SimpleREST\Logger\Syslog as Logger;
$logger = new Logger("myScriptLog");

use SimpleREST\File\EditableJSON as File;
$DATA   = new File( dirname(__FILE__) . "/data.json" );

use SimpleREST\Request as Request;
use SimpleREST\Response as Response;

$METHOD = Request::getMethod();
$PATH   = Request::getPath();

$logger->info("Request '$METHOD' '$PATH'");

switch ( $PATH ) {

# hello path...
case "/hello":

  switch ( $METHOD ) {

  # PUT /hello
  case "put":
    $DATA->hello = Request::getInput();

  # GET|HEAD /hello
  case "head":
  case "get":
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
    $DATA->setInternal( Request::getInput() );

  # GET /
  case "head":
  case "get":
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
