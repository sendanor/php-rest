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
		$params = $request->getParams();
		$values = array_values($params);
		$id = array_pop($values);
		return $this->fetch($id);
	}

	/** Removes the element */
	public function delete (iRequest $request) {
		$params = $request->getParams();
		$values = array_values($params);
		$id = array_pop($values);
		return $this->remove($id);
	}

	/** Changes the element */
	public function post (iRequest $request) {
		$params = $request->getParams();
		$values = array_values($params);
		$id = array_pop($values);
		$input = $request->getInput();
		return $this->update($id, $input);
	}

}
