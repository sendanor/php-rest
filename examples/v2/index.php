<?php

openlog("myScriptLog", LOG_PID | LOG_PERROR, LOG_LOCAL0);

// Import our core library
require( dirname(dirname(dirname(__FILE__))) . '/lib/REST2/index.php' );

$DATA = new REST2\JSONFile( dirname(__FILE__) . "/data.json" );

$method = REST2\Request::getMethod();
$path = REST2\Request::getPath();

syslog(LOG_INFO, "Request '$method' '$path'");

switch ( $method ) {

case "get":

  switch ( $path ) {

    case "/hello":
      REST2\Response::outputJSON( $DATA['hello'] );
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
      
  switch ( $path ) {

    case "/hello":
      $DATA['hello'] = REST2\Request::getInput();
      REST2\Response::outputJSON( $DATA['hello'] );
      break;

    case "/":
      $DATA = REST2\Request::getInput();
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
