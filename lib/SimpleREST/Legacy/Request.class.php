<?php
/* 
 * Sendanor SimpleREST PHP Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi> 
 */

namespace SimpleREST\Legacy;

use \SimpleREST\Request as SimpleRequest;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

/** */
class Request implements iRequest {

	private $method = null;
	private $path = null;
	private $query = null;
	private $input = null;
	private $params = null;
	private $router = null;

	/** */
	function __construct() {
		$this->method = SimpleRequest::getMethod();
		$this->path = SimpleRequest::getPath();
		$this->query = SimpleRequest::getQueryParams();
		$this->input = SimpleRequest::getInput();
		$this->params = array();
	}

	/** Returns the base URL to our front controller */
	public function getProtocol () {
		return isset($_SERVER['HTTPS']) ? 'https' : 'http';
	}

	/** Returns the base URL to our front controller */
	public function getPort () {
		return isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;
	}

	/** Returns the server part of the URL to our front controller */
	public function getHostname () {
		return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
	}

	/** Returns the full path of the URL */
	public function getFullPath () {
		return isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : (isset($_SERVER['REQUEST_URI']) ? substr($_SERVER['REQUEST_URI'], 0, strrchr($_SERVER['REQUEST_URI'], '?')): '/');
	}

	/** Returns the path of the URL to our front controller */
	public function getBasePath () {
		$full_path = $this->getFullPath();
		$path = $this->getPath();
		$base_path_length = strlen($full_path) - strlen($path);

		if (substr($full_path, $base_path_length) === $path) {
			return substr($full_path, 0, $base_path_length );
		}

		Log::write('full_path = ' . $full_path);
		Log::write('path = ' . $path);
		throw new Exception('Could not find base path!');
	}

	/** Returns the base URL to our front controller */
	public function getBaseURL () {
		return $this->getProtocol() . '://' . $this->getHostname() . $this->getBasePath();
	}

	/** Returns the full URL to current resource */
	public function getURL () {
		return rtrim($this->getBaseURL() . $this->getPath(), '/');
	}

	/** Returns the path for this resource */
	public function getPath () {
		return $this->path;
	}

	/** Returns params for this path */
	public function getParams () {
		return $this->params;
	}

	/** Returns current router */
	public function getRouter() {
		return $this->router;
	}

	/** Returns the method */
	public function getMethod() {
		return $this->method;
	}

	/** Returns the query params */
	public function getQuery() {
		return $this->query;
	}

	/** Returns the input */
	public function getInput() {
		return $this->input;
	}

	/** Set request route params */
	public function setParams(array $data) {
		$this->params = $data;
		return $this;
	}

	/** Set current router */
	public function setRouter(iRouter $r) {
		$this->router = $r;
		return $this;
	}

	/** Returns a URL to another resource, and $path may be relative */
	public function getRelativeURL ($path) {
		if (strlen($path) === 0) {
			return $this->getURL();
		}
		if ($path[0] === '/') {
			return $this->getBaseURL() . $path;
		}
		return $this->getURL() . '/' . $path;
	}

}
