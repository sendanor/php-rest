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

/** Collection of elements */
abstract class DatabaseCollection extends DatabaseResource implements iCollection {

	/** Get all rows from the database table */
	public function get (iRequest $request) {
		$query = $request->getQuery();
		return $this->select($query);
	}

	/** Create a new row in the database table */
	public function post (iRequest $request) {
		$input = $request->getInput();
		if (!is_array($input)) {
			throw new HTTPError(400, "input-invalid");
		}
		$id = $this->insert($input);
		return $this->fetch($id);
	}

}
