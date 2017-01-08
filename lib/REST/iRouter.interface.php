<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017 Jaakko-Heikki Heusala <jhh@sendanor.com> 
 */

namespace REST;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

interface iRouter {
	public function __construct ($paths=null);
	public function add ($path, $class_name);
	public function getClassName ($request_path);
	public function getResource ($request_path);
	public function getChildRoutes ($request_path);
}
