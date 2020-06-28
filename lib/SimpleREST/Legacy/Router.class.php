<?php
/* 
 * Sendanor SimpleREST PHP Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi> 
 */

namespace SimpleREST\Legacy;

use Exception;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

/** */
class Router implements iRouter {

	private $paths = array();
	private $resources = array();

	/** */
	public function __construct ($paths = null) {
		if(!is_array($paths)) {
			$paths = array();
		}
		foreach($paths as $path => $class_name) {
			$this->add($path, $class_name);
		}
	}

	/** Register a handler for path */
	public function add ($path, $class_name) {

		if(!is_string($path)) {
			throw new Exception('path must be string');
		}

		if(!is_string($class_name)) {
			throw new Exception('class must be string');
		}

		/*
		if (!class_exists($class_name)) {
			throw new Exception("Class not found: " . $class_name);
		}

		$interfaces = class_implements($class_name);
		if (!isset($interfaces['iResource'])) {
			throw new Exception("Class not iResource: ". $class_name);
		}

		$requestClass = new \ReflectionClass($class_name);
		if ($requestClass->isAbstract()) {
			throw new Exception("Class cannot be abstract: ". $class_name);
		}
		*/

		$this->paths[$path] = $class_name;
	}

	/** If these paths match, returns an array (with optional route parameters), otherwise NULL.
	 * @param $a The path which may have parameter definations as ":key"
	 * @param $b The path to match
	 */
	static protected function parsePathParams ($a, $b) {

		$a = explode("/", trim($a, "/"));
		$b = explode("/", trim($b, "/"));

		//Log::write( __FILE__ . ": a = " . var_export($a, true) );
		//Log::write( __FILE__ . ": b = " . var_export($b, true) );

		// Matching routes must have same size
		if ( count($a) !== count($b) ) {
			//Log::write( __FILE__ . ": Counts do not match" );
			return NULL;
		}

		$params = array();

		foreach ($a as $path) {
			$value = array_shift($b);

			// Collect parameter
			if ( (strlen($path) >= 2) && ($path[0] === ':') ) {
				$key = substr($path, 1);
				$params[$key] = $value;
				continue;
			}

			// Match paths
			if ($path !== $value) {
				//Log::write( __FILE__ . ": path='" . $path . "' does not match with '" . $value . "'" );
				return NULL;
			}

		}

		//Log::write( __FILE__ . ": params = " . var_export($params, true) );
		return $params;		
	}

	/** Returns the resource class name as a string for this request */
	public function getClassName ($request_path, &$save_params=NULL) {
		if(!is_string($request_path)) {
			return NULL;
		}
		foreach ($this->paths as $path => $class_name) {
			$params = $this->parsePathParams($path, $request_path);
			if(is_array($params)) {
				if(!is_null($save_params)) {
					$save_params = $params;
				}
				return $class_name;
			}
		}
		return NULL;
	}

	/** Returns the resource instance for this request */
	public function getResource ($request_path, &$save_params=NULL) {
		if(!is_string($request_path)) {
			return NULL;
		}
		$class_name = $this->getClassName($request_path, $save_params);
		if(!$class_name) {
			return NULL;
		}
		if(isset($this->resources[$class_name])) {
			return $this->resources[$class_name];
		}
		$resource = new $class_name();
		$this->resources[$class_name] = $resource;
		return $resource;
	}

	/** Returns an array of all child routes */
	public function getChildRoutes ($request_path) {
		if(!is_string($request_path)) {
			throw new Exception("request_path was not string");
		}
		$ret = array();
		//Log::write(__FILE__ . ': $request_path = ' . $request_path);

		// FIXME: $request_path might have a parameter value set!

		$b = rtrim($request_path, "/");
		//Log::write(__FILE__ . ': $b = ' . $b);
		foreach ($this->paths as $path => $class_name) {
			//Log::write(__FILE__ . ': $path = ' . $path);
			$a = rtrim($path, "/");
			//Log::write(__FILE__ . ': $a = ' . $a);
			if( strlen($a) <= strlen($b) ) {
				//Log::write(__FILE__ . ': skipped');
				continue;
			}
			if( substr($a, 0, strlen($b) + 1 ) === $b . "/" ) {
				//Log::write(__FILE__ . ': match: ' . $path);
				$ret[] = array(
					'path' => $path,
					'class_name' => $class_name
				);
			}
			//Log::write(__FILE__ . ': nothing done');
		}
		return $ret;
	}

}
