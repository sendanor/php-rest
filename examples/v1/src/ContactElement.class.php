<?php

/* Security check */
if (!defined('RESTExample')) {
	die("Direct access not permitted\n");
}

/** Contact Element */
class ContactElement extends SimpleREST\Legacy\Database\Element {

	/** */
	public function __construct() {
		parent::setTableName('contact');
	}

}
