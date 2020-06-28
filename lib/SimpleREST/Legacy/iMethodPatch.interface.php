<?php
/* 
 * Sendanor SimpleREST PHP Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi> 
 */

namespace SimpleREST\Legacy;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

interface iMethodPatch {

	/** HTTP PATCH action
	 * @param $request The optional request data
	 * @returns The output data for JSON serialization.
	 */
	public function patch (iRequest $request);

}
