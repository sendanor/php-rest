<?php
/*
 * Sendanor SimpleREST PHP Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\Legacy\Database;

use \SimpleREST\Legacy\API as API;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

/** Set database */
function setDefaultDatabase (iDatabase $db) {
	return API::setDatabase($db);
}
