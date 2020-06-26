<?php
/* 
 * Sendanor's PHP REST Library
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi> 
 */

namespace REST2;

if(!defined('REST_PHP')) {
	define('REST_PHP', TRUE);
}

require_once( dirname(__FILE__) . '/JSONFile.class.php' );
require_once( dirname(__FILE__) . '/HTTPError.class.php' );
require_once( dirname(__FILE__) . '/HTTPStatusMessages.class.php' );
require_once( dirname(__FILE__) . '/Request.class.php' );
require_once( dirname(__FILE__) . '/Response.class.php' );
