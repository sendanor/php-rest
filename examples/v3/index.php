<?php

require_once( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/index.php' );

use SimpleREST\Request as Request;

Request::run( function () {

  // These must be inside run() so that if errors happen (even fatal parse errors), the client will get correct error message.
  require_once( dirname(__FILE__) . '/config.php');
  require_once( dirname(__FILE__) . '/MyAPI.class.php' );

  Request::run(MyAPI::class);

  MyAPI::notFoundError();

});
