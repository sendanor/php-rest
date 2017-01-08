<?php

// Enable this file as accepted front controller for our example
define('RESTExample', TRUE);

// Read configuration from config.php
require('config.php');

// Import our framework
require( dirname(dirname(__FILE__)) . '/lib/REST/index.php');

// Add our own path to REST autoloader
REST\addAutoloadPath(dirname(__FILE__) . '/src');

/* Enable autoloader */
function __autoload ($className) {
	return REST\Autoloader::load($className);
}

// Setup default MySQL database connection
if(!defined('REST_TABLE_PREFIX')) {
    define('REST_TABLE_PREFIX', '');
}
$db = new REST\Database(REST_HOSTNAME, REST_USERNAME, REST_PASSWORD, REST_DATABASE);
$db->charset(REST_CHARSET);
$db->setTablePrefix(REST_TABLE_PREFIX);
REST\setDatabase($db);

// Enable automatic support for OPTIONS
REST\enableAutoOptions();

// Enable CORS
REST\setDefaultHeaders(array(
	'Access-Control-Allow-Origin' => '*'
));

// Run current request
REST\run(array(
	"/" => "RootElement",
	"/contact" => "ContactCollection",
	"/contact/:contact_id" => "ContactElement"
));
