<?php
/* 
 * Sendanor's PHP REST Library
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi> 
 */

namespace SimpleREST;

if(!defined('REST_PHP')) {
	define('REST_PHP', TRUE);
}

if(!defined('REST_PATH')) {
	define('REST_PATH', '');
}

require_once( dirname(__FILE__) . '/Assert.class.php' );
require_once( dirname(__FILE__) . '/Random.class.php' );
require_once( dirname(__FILE__) . '/JSON.class.php' );
require_once( dirname(__FILE__) . '/Validate.class.php' );
require_once( dirname(__FILE__) . '/Request.class.php' );
