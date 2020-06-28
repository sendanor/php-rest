<?php

/* Security check */
if (!defined('RESTExample')) {
	die("Direct access not permitted\n");
}

/** Contact collection */
class ContactCollection extends SimpleREST\Legacy\Database\DatabaseCollection {

	/** */
	public function __construct() {
		parent::setTableName('contact');
	}

}
