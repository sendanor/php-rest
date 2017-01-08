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

/** */
abstract class DatabaseResource extends Resource {

	protected $db = NULL;
	protected $table = NULL;

	/** Initialize */
	function init() {
		if(is_null($this->db)) {
			$this->db = API::getDatabase();
		}
	}

	/** */
	protected function setTable($table) {
		$this->table = $table;
		return $this;
	}

	/** SQL query */
	protected function query($query) {
		$this->init();
		return $this->db->query($query);
	}

	/** Escape for SQL query */
	protected function escape($query) {
		$this->init();
		return $this->db->escape($query);
	}

	/** Get a single row from database */
	protected function fetch($id) {
		$this->init();
		return $this->db->fetch($this->table, $id);
	}

	/** Get matching rows from database */
	protected function select(array $where) {
		$this->init();
		return $this->db->select($this->table, $where);
	}

	/** Insert data into table */
	protected function insert(array $data) {
		$this->init();
		return $this->db->insert($this->table, $data);
	}

}
