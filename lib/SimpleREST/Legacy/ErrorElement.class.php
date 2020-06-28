<?php

namespace SimpleREST\Framework;

use Exception;

/* Security check */
if (!defined('RESTExample')) {
	die("Direct access not permitted\n");
}

/** Special element to build JSON HTTP error messages for external web servers like Apache */
class ErrorElement extends Element {

	/** Returns a JSON object with code and error properties */
	function get (iRequest $request) {
		$params = $request->getParams();
		$values = array_values($params);
		$code = intval(end($values));
		return array(
			'error' => \SimpleREST\HTTPStatusMessages::getMessage($code),
			'code' => $code
		);
	}

}
