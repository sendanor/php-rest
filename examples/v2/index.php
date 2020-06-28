<?php

define('DATA_FILE', dirname(__FILE__) . "/data.json");

require( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/index.php' );
require( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/File/index.php' );
require( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/Log/index.php' );
require( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/Log/Syslog/index.php' );

use SimpleREST\File\JSON as JSON;
use SimpleREST\File\EditableJSON as EditableJSON;

use SimpleREST\Request as Request;
use SimpleREST\Response as Response;

SimpleREST\Log\setLogger( new SimpleREST\Log\Syslog("myScriptLog") );

$METHOD = Request::getMethod();
$PATH   = Request::getPath();

SimpleREST\Log\info("$METHOD $PATH: Request started");

switch ( $PATH ) {

# hello path...
case "/hello":

  switch ( $METHOD ) {

  # PUT /hello
  case "put":
    $DATA = new EditableJSON( DATA_FILE );
    $DATA->hello = Request::getInput();
    Response::outputJSON( $DATA->hello );

    SimpleREST\Log\info("$METHOD $PATH: Changed hello property as '" . $DATA->hello . "'");

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
    $DATA->setValue( Request::getInput() );
    Response::output( $DATA );
    SimpleREST\Log\info("$METHOD $PATH: Changed resource completely");
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
