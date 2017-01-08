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

/** Set database */
function setDatabase (iDatabase $db) {
	return API::setDatabase($db);
}
