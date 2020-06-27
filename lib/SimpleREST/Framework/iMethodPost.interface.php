<?php
/* 
 * Sendanor SimpleREST PHP Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi> 
 */

namespace SimpleREST\Framework;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

interface iResource {

	/** HTTP POST action
	 * @param $request The optional request data
	 * @returns The output data for JSON serialization.
	 */
	public function post (iRequest $request);

}
