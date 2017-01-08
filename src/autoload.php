<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017 Jaakko-Heikki Heusala <jhh@sendanor.com> 
 */

if(!defined('REST_PHP')) {
	define('REST_PHP', TRUE);
}

require_once( dirname(dirname(__FILE__)) . '/lib/REST/Autoloader.class.php');

/** Implements autoload functionality with support for checking existance 
 * with `class_exits()`. Returns true if class was successfully autoloaded, 
 * otherwise false.
 */
function __autoload ($className) {
	return REST\Autoloader::load($className);
}
