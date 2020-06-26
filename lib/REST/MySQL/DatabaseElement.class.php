<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017 Jaakko-Heikki Heusala <jhh@sendanor.com> 
 */

namespace REST\MySQL;

use REST\iElement;

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
		if (!$table->existsById($id)) {
			throw new HTTPError(404);
		}
		return $table->selectById($id);
	}

	/** Removes the element */
	public function delete (iRequest $request) {
		$table = $this->getTable();
		$params = $request->getParams();
		$values = array_values($params);
		$id = array_pop($values);
		if (!$table->existsById($id)) {
			throw new HTTPError(404);
		}
		$table->deleteById($id);
		return array('deleted' => 'success', 'id' => $id);
	}

	/** Changes the element */
	public function post (iRequest $request) {
		$table = $this->getTable();
		$params = $request->getParams();
		$values = array_values($params);
		$id = array_pop($values);
		if (!$table->existsById($id)) {
			throw new HTTPError(404);
		}
		$input = $request->getInput();
		$table->updateById($id, $input);
		return $table->selectById($id);
	}

}
