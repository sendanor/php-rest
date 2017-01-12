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

require_once( dirname(__FILE__) . '/Log.class.php');

/** */
class Autoloader {

	/** Paths where to load files */
	static protected $dirs = NULL;

	/** Init static values */
	static public function init () {
		if(is_null(self::$dirs)) {
			self::$dirs = array(dirname(dirname(__FILE__)));
		}
	}

	/** Implements autoload functionality with support for checking existance 
	 * with `class_exits()`. Returns true if class was successfully autoloaded, 
	 * otherwise false.
	 */
	static public function load ($className) {
		//Log::write("Autoloader::load($className)");
		$className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
		self::init();
		foreach(self::$dirs as $dir) {
			foreach(array('class', 'interface') as $type) {
				$file = $dir. '/' . $className . '.' . $type . '.php';
				if (file_exists($file)) {
					//Log::write("Loading " . $file);
					require_once $file;
					return true;
				}
			}

			$file = $dir. '/' . $className . '.php';
			if (file_exists($file)) {
				//Log::write("Loading " . $file);
				require_once $file;
				return true;
			}
		}
		return false;
	} 

	/** Add path to autoloader */
	static public function add ($path) {
		self::init();
		array_unshift(self::$dirs, $path);
	}

}
