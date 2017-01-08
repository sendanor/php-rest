<?php

// Enable this file as accepted front controller for our example
define('RESTExample', TRUE);

// Read configuration from config.php
require('config.php');

// Import our framework
require( dirname(dirname(__FILE__)) . '/lib/REST/index.php');

// Add our own path to REST autoloader
REST\addAutoloadPath(dirname(__FILE__));

/* Enable autoloader */
function __autoload ($className) {
	return REST\Autoloader::load($className);
}

// Setup default MySQL database connection
if(!defined('REST_TABLE_PREFIX')) {
    define('REST_TABLE_PREFIX', '');
}
$db = new REST\Database();
$db->connect(REST_HOSTNAME, REST_USERNAME, REST_PASSWORD, REST_DATABASE);
$db->charset(REST_CHARSET);
$db->setTablePrefix(REST_TABLE_PREFIX);
REST\setDatabase($db);

// Enable automatic support for OPTIONS
REST\enableAutoOptions();

// Run current request
REST\run(array(
	"/" => "RootElement",
	"/:email" => "UserRootElement",
	"/:email/client" => "ClientCollection",
	"/:email/client/:client_id" => "ClientElement",
	"/:email/client/:client_id/invoice" => "InvoiceCollection",
	"/:email/client/:client_id/invoice/:invoice_id" => "InvoiceElement",
	"/:email/client/:client_id/invoice/:invoice_id/row" => "InvoiceRowCollection",
	"/:email/client/:client_id/invoice/:invoice_id/row/:id" => "InvoiceRowElement",
	"/:email/client/:client_id/payment" => "PaymentCollection",
	"/:email/client/:client_id/payment/:id" => "PaymentElement",
	"/:email/client/:client_id/document" => "DocumentCollection",
	"/:email/client/:client_id/document/:id" => "DocumentElement",
	"/:email/client/:client_id/contact" => "ContactCollection",
	"/:email/client/:client_id/contact/:id" => "ContactElement"
));
