<?php

openlog("myScriptLog", LOG_PID | LOG_PERROR, LOG_LOCAL0);

// Import our core library
require( dirname(dirname(dirname(__FILE__))) . '/lib/REST2/index.php' );

// Import our JSON file library
require( dirname(dirname(dirname(__FILE__))) . '/lib/REST2/File/index.php' );

$DATA = new REST2\File\JSON( dirname(__FILE__) . "/data.json" );

$METHOD = REST2\Request::getMethod();
$PATH = REST2\Request::getPath();

syslog(LOG_INFO, "Request '$METHOD' '$PATH'");

switch ( $PATH ) {

# hello path...
case "/hello":

  switch ( $METHOD ) {

  # PUT /hello
  case "put":
    $DATA->hello = REST2\Request::getInput();

  # GET|HEAD /
  case "head":
  case "get":
    REST2\Response::outputJSON( $DATA->hello );
    break;

  # Other...
  default:
    REST2\Response::outputError(405);
    break;
  }
  break;


# Root path...
case "/":

  switch ( $METHOD ) {

  # PUT /
  case "put":
    $DATA->setInternal( REST2\Request::getInput() );

  # GET /
  case "head":
  case "get":
    REST2\Response::output( $DATA );
    break;

  # Other methods
  default:
    REST2\Response::outputError(405);
  }
  break;


# Any other path...
default:

  REST2\Response::outputError(404);

}
