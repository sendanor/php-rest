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

/** Hello test collection */
class Database implements iDatabase {

	private $link = NULL;
	private $table_prefix = '';

	/** */
	public function setTablePrefix($table) {
		$this->table_prefix = $table;
		return $this;
	}

	/** */
	public function getTablePrefix() {
		return $this->table_prefix;
	}

	/** Open MySQL connection */
	public function connect($hostname, $username, $password, $database) {
		if($this->link) {
			throw new Exception("Already connected to MySQL");
		}
		$this->link = mysqli_connect($hostname, $username, $password, $database);
		return $this;
	}

	/** Set charset */
	public function charset($charset) {
		mysqli_set_charset($this->link, $charset);
		return $this;
	}

	/** Close MySQL connection */
	public function __destruct() {
		if (!is_null($this->link)) {
			mysqli_close($this->link);
			$this->link = NULL;
		}
	}

	/** MySQL query */
	public function query($query) {
		$result = mysqli_query($this->link, $query);

		if (!$result) {
			return FALSE;
		}

		if($result === TRUE) {
			return TRUE;
		}

		$output = array();
		for ($i=0; $i<mysqli_num_rows($result); $i++) {
			$output[] = mysqli_fetch_assoc($result);
		}
		return $output;
	}

	/** */
	public function escapeIdentifier($str) {
		return str_replace('`', '``', $str);
	}

	/** */
	public function escapeTable($str) {
		return $this->escapeIdentifier($str);
	}

	/** */
	public function escapeColumn($str) {
		return $this->escapeIdentifier($str);
	}

	/** */
	public function escape($str) {
		return mysqli_real_escape_string($this->link, $str);
	}

	/** */
	public function error() {
		return mysqli_error($this->link);
	}

	/** */
	public function insertID() {
		return mysqli_insert_id($this->link);
	}

	/** Insert query */
	public function insert($table, $data) {
		if(!is_string($table)) {
			throw new Exception('Table not a string');
		}

		if(!is_array($data)) {
			throw new Exception('Data not an array');
		}

		$keys = array_keys($data);
		$values = array_values($data);

		foreach ($values as &$value) {
			$value = "'". $this->escape($value) . "'";
		}

		foreach ($keys as &$key) {
			$key = '`'. $this->escapeColumn($key) . '`';
		}

		$table = $this->table_prefix . $table;
		$query = 'INSERT INTO `'.$this->escapeTable($table).'` ('.implode(',', $keys).') VALUES ('.implode(',', $values).')';
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
