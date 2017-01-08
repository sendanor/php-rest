<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017 Jaakko-Heikki Heusala <jhh@sendanor.com> 
 */

namespace REST;

use Exception;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

/** */
class API {

	/** Default database connection */
	static protected $db = null;

	/** Default headers */
	static protected $headers = array();

	/** Array of supported methods */
	static protected $methods = array('options', 'get', 'post', 'delete', 'put', 'patch');

	/** If true, automatic documentation will be provided with OPTIONS method */
	static private $auto_options_enabled = false;

	/** Set a default headers */
	static public function setDefaultHeaders (array $headers) {
		foreach( $headers as $key => $value ) {
			self::$headers[$key] = $value;
		}
	}

	/** Set default database instance */
	static public function setDatabase (iDatabase $db) {
		self::$db = $db;
	}

	/** Returns the database instance */
	static public function getDatabase () {
		return self::$db;
	}

	/** Enable automatic documentation for this resource using the OPTIONS method */
	static public function enableAutoOptions () {
		self::$auto_options_enabled = true;
	}

	/** Disable automatic documentation for this resource using the OPTIONS method */
	static public function disableAutoOptions () {
		self::$auto_options_enabled = false;
	}

	/** Enable fetching automatic documentation for this resource using the OPTIONS method */
	static public function getAutoOptions () {
		return self::$auto_options_enabled;
	}

	/** Returns true if argument is valid method name */
	static public function isMethod($name) {
		return in_array($name, API::$methods);
	}

	/** Handle current request as a JSON REST request */
	static public function run ($routes) {
		$writer = new ResponseWriter();
		try {
			$writer->setDefaultHeaders(self::$headers);

			$request = new Request();

			if($routes instanceof iRouter) {
				$router = $routes;
			} else {
				$router = new Router($routes);
			}

			$request->setRouter($router);

			$params = array();

			$resource = $router->getResource($request->getPath(), $params);

			if(!$resource) {
				throw new HTTPError(404, "resource-not-found");
			}

			if(is_array($params)) {
				$request->setParams($params);
			}

			$method = $request->getMethod();

			if (!API::isMethod($method)) {
				throw new HTTPError(405, "method-not-supported");
			}

			if (!method_exists($resource, $method)) {
				throw new HTTPError(405, "no-method");
			}

			$data = $resource->$method($request);

			$writer->output($data);

		} catch (Exception $e) {
			$writer->outputException($e);
		}
	}

}
