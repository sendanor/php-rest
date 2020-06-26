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

/** Enable automatic documentation for this resource using the OPTIONS method */
function enableAutoOptions () {
	return API::enableAutoOptions();
}

/** Disable automatic documentation for this resource using the OPTIONS method */
function disableAutoOptions () {
	return API::disableAutoOptions();
}

/** Enable fetching automatic documentation for this resource using the OPTIONS method */
function getAutoOptions () {
	return API::getAutoOptions();
}

/** Returns true if argument is valid method name */
function isMethod ($name) {
	return API::isMethod($name);
}

/** Handle current request as a JSON REST request */
function run ($routes) {
	return API::run($routes);
}

/** Add path to autoloader */
function addAutoloadPath ($path) {
	return Autoloader::add($path);
}

/** Set default headers */
function setDefaultHeaders (array $headers) {
	return API::setDefaultHeaders($headers);
}

/** Returns a Redirect response instance */
function redirect ($request, $path=NULL) {

	if (is_null($path)) {
		$path = $request;
		$request = NULL;
	}	

	if ($path instanceof iRequest) {
		list($request, $path) = array($path, $request);
	}

	if (!is_string($path)) {
		throw new Exception('path is not string');
	}

	if (!($request instanceof iRequest)) {
		return new Redirect($path);
	}

	return new Redirect($request->getRelativeURL($path));
}
