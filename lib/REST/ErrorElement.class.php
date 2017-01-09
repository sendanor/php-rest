<?php

namespace REST;

use Exception;

/* Security check */
if (!defined('RESTExample')) {
	die("Direct access not permitted\n");
}

/** Special element to build JSON HTTP error messages for external web servers like Apache */
class ErrorElement extends Element {

	/** Doesn't return anything useful yet. Simply a hello world. */
	function get (iRequest $request) {
		$params = $request->getParams();
		$values = array_values($params);
		$code = intval(end($values));
		return array(
			'error' => HTTPStatusMessages::getMessage($code),
			'code' => $code
		);
	}

}
