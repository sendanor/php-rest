<?php

namespace REST;

use Exception;

/* Security check */
if (!defined('RESTExample')) {
	die("Direct access not permitted\n");
}

/** Special Collection to build JSON HTTP error messages for external web servers like Apache */
class ErrorCollection extends Collection {

	/** Returns a JSON object with all supported error messages as properties with links to invidual elements */
	function get (iRequest $request) {
		$body = array(
			'$ref' => $request->getURL()
		);
		$messages = HTTPStatusMessages::getAll();
		foreach ($messages as $key => $message) {
			$body[$key] = array(
				'$ref' => $request->getRelativeURL($key),
				'code' => $key,
				'error' => $message
			);
		}
		return $body;
	}

}
