<?php
/* 
 * Sendanor's PHP REST Library
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi> 
 */

namespace SimpleREST\Log;

if(!defined('REST_PHP')) {
	define('REST_PHP', TRUE);
}

require_once( dirname(__FILE__) . '/BaseLogger.class.php' );

require_once( dirname(__FILE__) . '/ErrorLog/index.php' );

require_once( dirname(__FILE__) . '/Manager.class.php' );

require_once( dirname(__FILE__) . '/functions.php' );

require_once( dirname(__FILE__) . '/Log.class.php' );
