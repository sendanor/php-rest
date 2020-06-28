<?php
/* 
 * Sendanor SimpleREST PHP Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi> 
 */

namespace SimpleREST\Framework\Database\MySQL;

use Exception;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

/** Interface for database tables */
class DatabaseTable implements SimpleREST\Framework\Database\iDatabaseTable {

	private $db = NULL;
	private $name = NULL;

	protected $insert_format       = 'INSERT INTO `%s` (%s) VALUES (%s)';

	protected $exists_byid_format  = 'SELECT COUNT(*) FROM `%s` WHERE `%s` = "%s" LIMIT 1';

	protected $select_where_format = 'SELECT %s FROM `%s` WHERE %s';
	protected $select_all_format   = 'SELECT %s FROM `%s`';
	protected $select_byid_format  = 'SELECT %s FROM `%s` WHERE `%s` = "%s" LIMIT 1';

	protected $update_where_format = 'UPDATE `%s` SET %s WHERE %s';
	protected $update_all_format   = 'UPDATE `%s` SET %s';
	protected $update_byid_format  = 'UPDATE `%s` SET %s WHERE `%s` = "%s" LIMIT 1';

	protected $delete_where_format = 'DELETE FROM `%s` WHERE %s';
	protected $delete_all_format   = 'DELETE FROM `%s`';
	protected $delete_byid_format  = 'DELETE FROM `%s` WHERE `%s` = "%s" LIMIT 1';

	/** Construct an interface for database table */
	public function __construct (SimpleREST\Framework\Database\iDatabase $db, $name) {
		$this->db = $db;
		$this->name = $name;
	}

	/** Set database interface */
	public function setDatabase (SimpleREST\Framework\Database\iDatabase $db) {
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
		$query = sprintf($this->insert_format, $this->db->escapeTable($table), implode(', ', $keys), implode(', ', $values));
		if(!$this->db->query($query)) {
			Log::write('query = ' . $query);
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
		$rows = $this->db->query($query);
		if ($rows === FALSE) {
			Log::write(__FILE__ . ': query = ' . $query);
			throw new Exception('Select failed: ' . $this->db->lastError());
		}
		return $rows;
	}

	/** Select multiple rows */
	public function selectAll () {
		return $this->select(array());
	}

	/** Returns true if row with this primary key exists */
	public function existsById ($id) {
		$table = $this->getTable();
		$primary_key = $this->getPrimaryKey();
		$query = sprintf($this->exists_byid_format, $this->db->escapeTable($table), $this->db->escapeColumn($primary_key), $this->db->escape($id));
		$result = $this->db->query($query);
		if ($result === FALSE) {
			throw new Exception('[Row] exists by ID failed: ' . $this->db->lastError());
		}
		$values = array_values(array_shift($result));
		return intval(array_shift($values)) === 1;
	}

	/** Fetch single row by primary key */
	public function selectById ($id) {
		$table = $this->getTable();
		$columns = '*';
		$primary_key = $this->getPrimaryKey();
		$query = sprintf($this->select_byid_format, $columns, $this->db->escapeTable($table), $this->db->escapeColumn($primary_key), $this->db->escape($id));
		$result = $this->db->query($query);
		if ($result === FALSE) {
			throw new Exception('Select by ID failed: ' . $this->db->lastError());
		}
		return array_shift($result);
	}

	/** Update rows */
	public function update (array $where, array $data) {
		$table = $this->getTable();
		$set = array();
		foreach ($data as $key => $value) {
			$set[] = '`' . $this->db->escapeColumn($key) . '` = \'' . $this->db->escape($value) . "'";
		}
		$values = array();
		foreach ($where as $key => $value) {
			$values[] = '`' . $this->db->escapeColumn($key) . '` = \'' . $this->db->escape($value) . "'";
		}
		if (count($set) < 1) {
			throw new Exception("Empty data array -- nothing to update");
		}
		if (count($where) < 1) {
			throw new Exception("If you really want to update the whole table, use .updateAll()");
		}
		$query = sprintf($this->update_where_format, $this->db->escapeTable($table), implode(', ', $set), implode(' AND ', $values));
		$result = $this->db->query($query);
		if ($result === FALSE) {
			Log::write(__FILE__ . ': query = ' . $query);
			throw new Exception('Update failed: ' . $this->db->lastError());
		}
		return $result;
	}

	/** Update rows */
	public function updateAll (array $data) {
		$table = $this->getTable();
		$set = array();
		foreach ($data as $key => $value) {
			$set[] = '`' . $this->db->escapeColumn($key) . '` = \'' . $this->db->escape($value) . "'";
		}
		if (count($set) < 1) {
			throw new Exception("Empty data array -- nothing to update");
		}
		$query = sprintf($this->update_all_format, $this->db->escapeTable($table), implode(', ', $set));
		$result = $this->db->query($query);
		if ($result === FALSE) {
			Log::write(__FILE__ . ': query = ' . $query);
			throw new Exception('Update failed: ' . $this->db->lastError());
		}
		return $result;
	}

	/** Update rows by ID */
	public function updateById ($id, array $data) {
		$table = $this->getTable();
		$primary_key = $this->getPrimaryKey();
		$set = array();
		foreach ($data as $key => $value) {
			$set[] = '`' . $this->db->escapeColumn($key) . '` = \'' . $this->db->escape($value) . "'";
		}
		if (count($set) < 1) {
			throw new Exception("Empty data array -- nothing to update");
		}
		$query = sprintf($this->update_byid_format, $this->db->escapeTable($table), implode(', ', $set), $this->db->escapeColumn($primary_key), $this->db->escape($id) );
		$result = $this->db->query($query);
		if ($result === FALSE) {
			Log::write(__FILE__ . ': query = ' . $query);
			throw new Exception('Update by ID failed: ' . $this->db->lastError());
		}
		return $result;
	}

	/* Delete rows */
	public function delete (array $where) {
		$table = $this->getTable();
		$values = array();
		foreach ($where as $key => $value) {
			$values[] = '`' . $this->db->escapeColumn($key) . '` = \'' . $this->db->escape($value) . "'";
		}
		if (count($values) < 1) {
			throw new Exception("If you really want to delete the whole table, use .deleteAll()");
		}
		$query = sprintf($this->delete_where_format, $this->db->escapeTable($table), implode(' AND ', $values));
		$result = $this->db->query($query);
		if ($result === FALSE) {
			Log::write(__FILE__ . ': query = ' . $query);
			throw new Exception('Delete failed: ' . $this->db->lastError());
		}
		return $result;
	}

	/* Delete rows */
	public function deleteAll () {
		$table = $this->getTable();
		$query = sprintf($this->delete_all_format, $this->db->escapeTable($table));
		$result = $this->db->query($query);
		if ($result === FALSE) {
			Log::write(__FILE__ . ': query = ' . $query);
			throw new Exception('Delete failed: ' . $this->db->lastError());
		}
		return $result;
	}

	/** Delete row by primary key */
	public function deleteById ($id) {
		$table = $this->getTable();
		$primary_key = $this->getPrimaryKey();
		$query = sprintf($this->delete_byid_format, $this->db->escapeTable($table), $this->db->escapeColumn($primary_key), $this->db->escape($id) );
		$result = $this->db->query($query);
		if ($result === FALSE) {
			Log::write(__FILE__ . ': query = ' . $query);
			throw new Exception('Delete by ID failed: ' . $this->db->lastError());
		}
		return $result;
	}

}
