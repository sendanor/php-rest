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

	private $link = NULL;
	private $prefix = '';
	private $name = '';

	/** */
	public function __construct($table) {
		$this->name = $table;
	}

	/** */
	public function setName($table) {
		$this->name = $table;
		return $this;
	}

	/** */
	public function getName() {
		return $this->name;
	}

	/** */
	public function setPrefix($table) {
		$this->prefix = $table;
		return $this;
	}

	/** */
	public function getPrefix() {
		return $this->prefix;
	}

	/** Set database interface */
	public function setLink($link) {
		$this->link = $link;
		return $this;
	}

	/** Insert query */
	public function insert(array $data) {

		$table = $this->name;

		if(!is_string($table)) {
			throw new Exception('Table name not set');
		}

		$keys = array_keys($data);
		$values = array_values($data);

		foreach ($values as &$value) {
			$value = "'". $this->link->escape($value) . "'";
		}

		foreach ($keys as &$key) {
			$key = '`'. $this->link->escapeColumn($key) . '`';
		}

		$table = $this->table_prefix . $table;
		$query = 'INSERT INTO `'.$this->link->escapeTable($table).'` ('.implode(',', $keys).') VALUES ('.implode(',', $values).')';
		Log::write('query = ' . $query);
		if(!$this->query($query)) {
			throw new Exception('Insert failed: ' . $this->error());
		}
		return $this->insertID();
	}

	/** Select rows */
	public function select ($table, array $where) {

		if(!is_string($table)) {
			throw new Exception('Table not a string');
		}

		$values = array();
		foreach ($where as $key => $value) {
			$values[] = '`' . $this->escapeColumn($key) . '` = \'' . $this->escape($value) . "'";
		}

		$table = $this->table_prefix . $table;
		$query = 'SELECT * FROM `'.$this->escapeTable($table).'`';
		if(count($values) >= 1) {
			$query .= ' WHERE ' . implode(' AND ', $values);
		}
		Log::write(__FILE__ . ': query = ' . $query);
		return $this->query($query);
	}

	/** Fetch a row */
	public function fetch ($table, $id) {

		if(!is_string($table)) {
			throw new Exception('Table not a string');
		}

		$format = 'SELECT * FROM `%s` WHERE `%s` = "%s" LIMIT 1';
		$primary_key = $table . '_id';
		$table = $this->table_prefix . $table;
		$query = sprintf($format, $this->escapeTable($table), $this->escapeColumn($primary_key), $this->escape($id));
		$result = $this->query($query);

		if(!$result) {
			return NULL;
		}

		return array_shift($result);
	}

	/** Update rows */
	public function update ($table, array $where, array $data) {

		if(!is_string($table)) {
			throw new Exception('table not a string');
		}

		if(!is_array($where)) {
			throw new Exception('where not an array');
		}

		if(!is_array($data)) {
			throw new Exception('data not an array');
		}

		$values = array();
		foreach ($where as $key => $value) {
			$values[] = '`' . $this->escape($key) . '` = \'' . $this->escape($value) . "'";
		}

		$table = $this->table_prefix . $table;
		$query = 'UPDATE * FROM `'.$table.'`';
		if(count($values) >= 1) {
			$query .= ' WHERE ' . implode(' AND ', $values);
		}
		Log::write(__FILE__ . ': query = ' . $query);
		return $this->query($query);
	}

}
