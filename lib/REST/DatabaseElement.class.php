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

/** Database powered REST element */
abstract class DatabaseElement extends DatabaseResource implements iElement {

	/** Returns the database row */
	public function get (iRequest $request) {
		$params = $request->getParams();
		$values = array_values($params);
		$id = array_pop($values);
		return $this->fetch($id);
	}

}
