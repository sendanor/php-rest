<?php

/* Security check */
if (!defined('RESTExample')) {
	die("Direct access not permitted\n");
}

/** Contact collection */
class ContactCollection extends REST\DatabaseCollection {
	/** */
	public function __construct() {
		parent::setTable('contact');
	}
}
