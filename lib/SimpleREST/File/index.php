<?php
/* 
 * Sendanor's PHP REST Library
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi> 
 */
declare(strict_types=1);

namespace SimpleREST\File;

if(!defined('REST_PHP')) {
	define('REST_PHP', TRUE);
}

require_once( dirname(__FILE__) . '/Lock/index.php' );
require_once( dirname(__FILE__) . '/Text/index.php' );
require_once( dirname(__FILE__) . '/JSON/index.php' );
