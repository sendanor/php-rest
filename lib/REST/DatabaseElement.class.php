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

/** Implementation of row in a database table as a REST element resource. The 
 * last parameter in the URL must be the primary key of the row.
 */
abstract class DatabaseElement extends DatabaseResource implements iElement {

	/** Returns the element */
	public function get (iRequest $request) {
		$table = $this->getTable();
		$params = $request->getParams();
		$values = array_values($params);
		$id = array_pop($values);
		return $table->fetch($id);
	}

	/** Removes the element */
	public function delete (iRequest $request) {
		$table = $this->getTable();
		$params = $request->getParams();
		$values = array_values($params);
		$id = array_pop($values);
		return $table->delete($id);
	}

	/** Changes the element */
	public function post (iRequest $request) {
		$table = $this->getTable();
		$params = $request->getParams();
		$values = array_values($params);
		$id = array_pop($values);
		$input = $request->getInput();
		return $table->update($id, $input);
	}

}
