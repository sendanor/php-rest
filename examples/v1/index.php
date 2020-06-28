<?php

// Enable this file as accepted front controller for our example
define('RESTExample', TRUE);

// Read configuration from config.php
require('config.php');

// Import our core framework
require( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/Legacy/index.php');

// Enable database support
require( dirname(dirname(dirname(__FILE__))) . '/lib/SimpleREST/Legacy/Database/index.php');

// Add our own path to REST autoloader
\SimpleREST\Legacy\addAutoloadPath(dirname(__FILE__) . '/src');

// Enable autoloader
function __autoload ($className) {
	return \SimpleREST\Legacy\Autoloader::load($className);
}

// Setup default MySQL database connection
if (!defined('REST_TABLE_PREFIX')) {
    define('REST_TABLE_PREFIX', '');
}
$db = new \SimpleREST\Legacy\Database\MySQL\Database(REST_HOSTNAME, REST_USERNAME, REST_PASSWORD, REST_DATABASE);
$db->charset(REST_CHARSET);
$db->setTablePrefix(REST_TABLE_PREFIX);
\SimpleREST\Legacy\Database\setDefaultDatabase($db);

// Enable automatic support for OPTIONS
\SimpleREST\Legacy\enableAutoOptions();

// Enable CORS
\SimpleREST\Legacy\setDefaultHeaders(array(
	'Access-Control-Allow-Origin' => '*'
));

// Run current request
\SimpleREST\Legacy\run(array(
	"/" => "RootElement",
	"/contact" => "ContactCollection",
	"/contact/:contact_id" => "ContactElement",
	"/error" => "\SimpleREST\Legacy\ErrorCollection",
	"/error/:error_id" => "\SimpleREST\Legacy\ErrorElement"
));
