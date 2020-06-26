<?php

// Import our core library
require( dirname(dirname(dirname(__FILE__))) . '/lib/REST2/index.php' );

switch (REST2\Request::getPath()) {

case "/hello":
	REST2\Request::outputJSON( "world" );
	break;

case "/":

	REST2\Request::output( array( "hello" => "world" ) );
	break;

default:

	REST2\Request::outputError(404);

}
