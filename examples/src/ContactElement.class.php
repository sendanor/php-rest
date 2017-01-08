<?php

/* Security check */
if (!defined('RESTExample')) {
	die("Direct access not permitted\n");
}

/** Contact Element */
class ContactElement extends REST\DatabaseElement {

	/** */
	public function __construct() {
		parent::setTable('contact');
	}

}
