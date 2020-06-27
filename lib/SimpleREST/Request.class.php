<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko-Heikki Heusala <jheusala@iki.fi>
 */

namespace SimpleREST;

use Exception;

class Request {

        private static $method = null;
        private static $path = null;
        private static $input = null;

	/** Returns current method name, all lowercase characters. */
	public static function getMethod () {
		if (self::$method == null) {
			self::$method = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'get';
		}
		return self::$method;
	}

	/**
	 */
	public static function isMethodGet() {
		return self::getMethod() === 'get';
	}

	/** Returns current request path */
	public static function getPath () {

		if (self::$path == null) {

			if ( isset($_SERVER['PATH_INFO']) && strlen($_SERVER['PATH_INFO']) >= 1 ) {

				self::$path = $_SERVER['PATH_INFO'];

			} else if ( isset($_SERVER['ORIG_PATH_INFO']) ) {

				self::$path = $_SERVER['ORIG_PATH_INFO'];

			} else {

				self::$path = "/";

			}

		}

		return self::$path;

	}

	/** Returns current request query params */
	public static function getQueryParams () {
		return (array)$_GET;
	}

	/** Returns current request input as JSON */
	public static function getInput () {

		if ( self::$input == null ) {
			if ( self::isMethodGet() ) {
				self::$input = array();
			} else {
				self::$input = json_decode(file_get_contents('php://input'), true);
			}
		}

		return self::$input;

	}

}
