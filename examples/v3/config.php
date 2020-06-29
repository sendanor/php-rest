<?php

// If enabled, will not use development features
//define('REST_PRODUCTION', true);

// Set logger (syslog or stderr)
define('REST_LOGGER', 'stderr');

// The data for JSON file example
define('REST_DATA_FILE', dirname(__FILE__) . "/data.json");

// Database settings
define('REST_DATABASE', 'phprestexample');
define('REST_HOSTNAME', 'localhost');
define('REST_USERNAME', 'phprestexample');
define('REST_PASSWORD', 'secret');
define('REST_CHARSET', 'utf8');
