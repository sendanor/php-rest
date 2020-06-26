<?php
/*
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko-Heikki Heusala <jheusala@iki.fi>
 */

namespace REST\MySQL;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

/** Database interface */
interface iDatabaseTable {
	public function __construct (iDatabase $db, $name);
	public function setDatabase (iDatabase $db);
	public function setName ($name);
	public function getName ();
	public function getPrefix ();
	public function getTable ();
	public function getPrimaryKey ();

	public function insert (array $data);

	public function select (array $where);
	public function selectAll ();
	public function selectById ($id);

	public function update (array $where, array $data);
	public function updateAll (array $data);
	public function updateById ($id, array $data);

	public function delete (array $where);
	public function deleteAll ();
	public function deleteById ($id);

}
