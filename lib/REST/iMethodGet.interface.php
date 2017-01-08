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

interface iMethodGet {

	/** HTTP GET action
	 * @param $request The optional request data
	 * @returns The output data for JSON serialization.
	 */
	public function get (iRequest $request);

}
