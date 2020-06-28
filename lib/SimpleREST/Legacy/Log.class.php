<?php
/* 
 * Sendanor SimpleREST PHP Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi> 
 */

namespace SimpleREST\Legacy;

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
