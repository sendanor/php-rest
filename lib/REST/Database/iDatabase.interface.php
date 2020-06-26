<?php
/*
 * Sendanor's PHP REST Framework
 * Copyright 2017 Jaakko-Heikki Heusala <jhh@sendanor.com>
 */

namespace REST\Database;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

/** Database interface */
interface iDatabase {

	public function setTablePrefix ($table);
	public function getTablePrefix ();
	public function query ($query);
	public function escapeIdentifier ($str);
	public function escapeTable ($str);
	public function escapeColumn ($str);
	public function escape ($str);
	public function lastError ();
	public function lastInsertID ();
	public function setTable ($name, iDatabaseTable $table);
	public function getTable ($name);

}
