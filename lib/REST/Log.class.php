<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017 Jaakko-Heikki Heusala <jhh@sendanor.com> 
 */

namespace REST;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

/** Log to stderr */
class Log {

	static public $stderr = NULL;

	static public function write($msg) {
		if(is_null(self::$stderr)) {
			self::$stderr = fopen('php://stderr', 'w');
		}
		fwrite(self::$stderr, "$msg\n");
	}

}
