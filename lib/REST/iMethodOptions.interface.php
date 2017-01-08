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

interface iMethodOptions {

	/** HTTP OPTIONS action
	 * @returns The output data for JSON serialization.
	 */
	public function options (iRequest $request);

}
