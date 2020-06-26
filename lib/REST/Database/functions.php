<?php
/*
 * Sendanor's PHP REST Framework
 * Copyright 2017 Jaakko-Heikki Heusala <jhh@sendanor.com>
 */

namespace REST\Database;

use \REST\API as API;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

/** Set database */
function setDefaultDatabase (iDatabase $db) {
	return API::setDatabase($db);
}
