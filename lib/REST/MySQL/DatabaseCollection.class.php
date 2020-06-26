<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017 Jaakko-Heikki Heusala <jhh@sendanor.com> 
 */

namespace REST\MySQL;

use REST\iCollection;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

/** Implementation of a database table as a REST collection resource */
abstract class DatabaseCollection extends DatabaseResource implements iCollection {

	/** Returns all elements in the collection. You may use query params to 
	 * limit matches.
	 */
	public function get (iRequest $request) {
		$table = $this->getTable();
		$query = $request->getQuery();
		return $table->select($query);
	}

	/** Create a new element in the collection */
	public function post (iRequest $request) {
		$table = $this->getTable();
		$input = $request->getInput();
		if (!is_array($input)) {
			throw new HTTPError(400, "input-invalid");
		}
		$id = $table->insert($input);
		return $table->selectById($id);
	}

}
