<?php

/* Security check */
if (!defined('RESTExample')) {
	die("Direct access not permitted\n");
}

use REST\Element;
use REST\iRequest;

/** The root resource for this REST service */
class RootElement extends Element {

	/** Doesn't return anything useful yet. Simply a hello world. */
	function get (iRequest $request) {
		return array(
			'hello' => 'world'
		);
	}

}
