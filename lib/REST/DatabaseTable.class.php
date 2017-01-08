<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017 Jaakko-Heikki Heusala <jhh@sendanor.com> 
 */

namespace REST;

use Exception;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

/** Interface for database tables */
class DatabaseTable implements iDatabaseTable {

	private $db = NULL;
	private $name = NULL;

	protected $insert_format       = 'INSERT INTO `%s` (%s) VALUES (%s)';
	protected $select_where_format = 'SELECT %s FROM `%s` WHERE %s';
	protected $select_all_format   = 'SELECT %s FROM `%s`';
	protected $fetch_format        = 'SELECT %s FROM `%s` WHERE `%s` = "%s" LIMIT 1';
	protected $update_where_format = 'UPDATE `%s` SET %s WHERE %s';
	protected $update_all_format   = 'UPDATE `%s` SET %s';
	protected $delete_where_format = 'DELETE FROM `%s` WHERE %s';
	protected $delete_all_format   = 'DELETE FROM `%s`';

	/** Construct an interface for database table */
	public function __construct (iDatabase $db, $name) {
		$this->db = $db;
		$this->name = $name;
	}

	/** Set database interface */
	public function setDatabase (iDatabase $db) {
		$this->db = $db;
		return $this;
	}

	/** Set the table name */
	public function setName ($name) {
		$this->name = $name;
		return $this;
	}

	/** Returns the name of the table without prefix */
	public function getName () {
		if(!is_string($this->name)) {
			throw new Exception('Table name not set');
		}
		return $this->name;
	}

	/** Get the optional table prefix */
	public function getPrefix () {
		return $this->db->getTablePrefix();
	}

	/** Returns the name of the table with prefix */
	public function getTable () {
		return $this->getPrefix() . $this->getName();
	}

	/** Returns the primary keyword name for this table */
	public function getPrimaryKey () {
		return $this->getName() . '_id';
	}

	/** Insert query */
	public function insert (array $data) {
		$table = $this->getTable();
		$keys = array_keys($data);
		$values = array_values($data);
		foreach ($values as &$value) {
			$value = "'". $this->db->escape($value) . "'";
		}
		foreach ($keys as &$key) {
			$key = '`'. $this->db->escapeColumn($key) . '`';
		}
		$query = sprintf($this->insert_format, $this->db->escapeTable($table), implode(', ', $keys), implode(', ', $values);
		Log::write('query = ' . $query);
		if(!$this->db->query($query)) {
			throw new Exception('Insert failed: ' . $this->db->lastError());
		}
		return $this->db->lastInsertID();
	}

	/** Select multiple rows */
	public function select (array $where) {
		$table = $this->getTable();
		$columns = '*';
		$values = array();
		foreach ($where as $key => $value) {
			$values[] = '`' . $this->db->escapeColumn($key) . '` = \'' . $this->db->escape($value) . "'";
		}
		if (count($values) >= 1) {
			$query = sprintf($this->select_where_format, $columns, $this->db->escapeTable($table), implode(' AND ', $values));
		} else {
			$query = sprintf($this->select_all_format, $columns, $this->db->escapeTable($table));
		}
		Log::write(__FILE__ . ': query = ' . $query);
		$rows = $this->db->query($query);
		if (!$rows) {
			throw new Exception('Select failed: ' . $this->db->lastError());
		}
		return $rows;
	}

	/** Fetch single row by primary key */
	public function fetch ($id) {
		$table = $this->getTable();
		$columns = '*';
		$primary_key = $this->getPrimaryKey();
		$query = sprintf($this->fetch_format, $columns, $this->escapeTable($table), $this->escapeColumn($primary_key), $this->escape($id));
		$result = $this->query($query);
		if (!$result) {
			throw new Exception('Fetch failed: ' . $this->db->lastError());
		}
		return array_shift($result);
	}

	/** Update rows */
	public function update (array $where, array $data) {
		$table = $this->getTable();
		$set = array();
		foreach ($data as $key => $value) {
			$set[] = '`' . $this->escapeColumn($key) . '` = \'' . $this->escape($value) . "'";
		}
		$values = array();
		foreach ($where as $key => $value) {
			$values[] = '`' . $this->escapeColumn($key) . '` = \'' . $this->escape($value) . "'";
		}
		if (count($values) >= 1) {
			$query = sprintf($this->update_where_format, $table, implode(', ', $set), implode(' AND ', $values));
		} else {
			$query = sprintf($this->update_all_format, $table, implode(', ', $set));
		}
		Log::write(__FILE__ . ': query = ' . $query);
		$result = $this->query($query);
		if (!$result) {
			throw new Exception('Update failed: ' . $this->db->lastError());
		}
		return $result;
	}

	/** Delete rows */
	public function delete (array $where) {
		$table = $this->getTable();
		$values = array();
		foreach ($where as $key => $value) {
			$values[] = '`' . $this->escapeColumn($key) . '` = \'' . $this->escape($value) . "'";
		}
		if (count($values) >= 1) {
			$query = sprintf($this->delete_where_format, $table, implode(' AND ', $values));
		} else {
			$query = sprintf($this->delete_all_format, $table);
		}
		Log::write(__FILE__ . ': query = ' . $query);
		$result = $this->query($query);
		if (!$result) {
			throw new Exception('Result failed: ' . $this->db->lastError());
		}
		return $result;
	}

}
