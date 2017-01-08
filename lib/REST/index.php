<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017 Jaakko-Heikki Heusala <jhh@sendanor.com> 
 */

namespace REST;
if(!defined('REST_PHP')) {
	define('REST_PHP', TRUE);
}
require_once( dirname(__FILE__) . '/Autoloader.class.php' );
require_once( dirname(__FILE__) . '/fatal_error_handler.php' );
require_once( dirname(__FILE__) . '/functions.php' );
