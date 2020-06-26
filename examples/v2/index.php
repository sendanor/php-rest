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

switch ( $METHOD ) {

case "head":
case "get":

  switch ( $PATH ) {

    case "/hello":
      REST2\Response::outputJSON( $DATA->hello );
      break;

    case "/":
      REST2\Response::output( $DATA );
      break;

    default:
      REST2\Response::outputError(404);
      break;

  }

  break;

case "put":
      
  switch ( $PATH ) {

    case "/hello":
      $DATA->hello = REST2\Request::getInput();
      REST2\Response::outputJSON( $DATA->hello );
      break;

    case "/":
      $DATA->setInternal( REST2\Request::getInput() );
      REST2\Response::output( $DATA );
      break;

    default:
      REST2\Response::outputError(404);
      break;

  }

  break;

default:
  REST2\Response::outputError(405);
  break;

}
