<?php
/*
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko-Heikki Heusala <jheusala@iki.fi>
 */

namespace REST\MySQL;

use Exception;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

/** Hello test collection */
class Database implements iDatabase {

	/** MySQL connection object */
	private $link = NULL;

	/** The prefix of table names */
	private $table_prefix = '';

	/** An array of table objects for this database */
	private $tables = NULL;

	/** Construct a database interface */
	public function __construct ($hostname=NULL, $username=NULL, $password=NULL, $database=NULL, $charset=NULL) {
		if(func_num_args() >= 5) {
			$this->connect($hostname, $username, $password, $database);
			$this->charset($charset);
			return;
		}
		if(func_num_args() !== 0) {
			$this->connect($hostname, $username, $password, $database);
		}
	}

	/** Close MySQL connection */
	public function __destruct () {
		if (!is_null($this->link)) {
			mysqli_close($this->link);
			$this->link = NULL;
		}
	}

	/** */
	public function setTablePrefix ($table) {
		$this->table_prefix = $table;
		return $this;
	}

	/** */
	public function getTablePrefix () {
		return $this->table_prefix;
	}

	/** Open MySQL connection */
	public function connect ($hostname, $username, $password, $database) {
		if($this->link) {
			throw new Exception("Already connected to MySQL");
		}
		$this->link = mysqli_connect($hostname, $username, $password, $database);
		return $this;
	}

	/** Set charset */
	public function charset ($charset) {
		mysqli_set_charset($this->link, $charset);
		return $this;
	}

	/** MySQL query */
	public function query ($query) {
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
	public function escapeIdentifier ($str) {
		return str_replace('`', '``', $str);
	}

	/** */
	public function escapeTable ($str) {
		return $this->escapeIdentifier($str);
	}

	/** */
	public function escapeColumn ($str) {
		return $this->escapeIdentifier($str);
	}

	/** */
	public function escape ($str) {
		return mysqli_real_escape_string($this->link, $str);
	}

	/** */
	public function lastError () {
		return mysqli_error($this->link);
	}

	/** */
	public function lastInsertID () {
		return mysqli_insert_id($this->link);
	}

	/** Set a table interface */
	protected function initTables() {
		if (is_null($this->tables)) {
			$this->tables = array();
		}
	}

	/** Set a table interface */
	public function setTable($name, iDatabaseTable $table) {
		if (!is_string($name)) {
			throw new Exception('name must be string');
		}
		$this->initTables();
		$this->tables[$name] = $table;
		return $this;
	}

	/** Returns the table interface */
	public function getTable($name) {
		if (!is_string($name)) {
			throw new Exception('name must be string');
		}
		$this->initTables();
		if (!isset($this->tables[$name])) {
			$this->tables[$name] = new DatabaseTable($this, $name);
		}
		return $this->tables[$name];
	}

}
