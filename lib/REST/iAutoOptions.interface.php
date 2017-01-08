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

interface iAutoOptions {

	/** Enable fetching automatic documentation for this resource using the OPTIONS method */
	public function enableAutoOptions ();

	/** Enable fetching automatic documentation for this resource using the OPTIONS method */
	public function disableAutoOptions ();

	/** Enable fetching automatic documentation for this resource using the OPTIONS method */
	public function getAutoOptions ();

}
