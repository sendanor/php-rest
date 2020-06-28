<?php
/*
 * Sendanor SimpleREST PHP Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\Legacy\Database;

use \SimpleREST\Legacy\iRequest as iRequest;
use \SimpleREST\Legacy\iCollection as iCollection;

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
			throw new \SimpleREST\HTTPError(400, "input-invalid");
		}
		$id = $table->insert($input);
		return $table->selectById($id);
	}

}
