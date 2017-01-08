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
		$this->method = strtolower( isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get' );
		$this->path = isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '/';
		$this->query = (array)$_GET;
		if($this->method === "get") {
			$this->input = $this->query;
		} else {
			$this->input = json_decode(file_get_contents('php://input'), true);
		}
		$this->params = array();
	}

	/** Returns the path for this resource */
	public function getPath() {
		return $this->path;
	}

	/** Returns params for this path */
	public function getParams() {
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

}
