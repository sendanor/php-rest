<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017 Jaakko-Heikki Heusala <jhh@sendanor.com> 
 */

namespace REST\MySQL;

use REST\Resource;

/* Security check */
if (!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

/** Base class for database driven REST resources */
abstract class DatabaseResource extends Resource {

	/** The database table interface */
	protected $table = NULL;

	/** Returns the database interface */
	protected function getDatabase () {
		return API::getDatabase();
	}

	/** Set the database table name without prefixes */
	protected function setTableName ($table) {
		if (!is_string($table)) {
			throw new Exception('Table name must be a string');
		}
		$db = $this->getDatabase();
		$this->table = $db->getTable($table);
		return $this;
	}

	/** Returns interface for $this->table */
	protected function getTable () {
		if(!$this->table) {
			throw new Exception('.setTableName() must be used first');
		}
		return $this->table;
	}

}
