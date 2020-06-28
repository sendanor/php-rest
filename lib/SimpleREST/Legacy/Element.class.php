<?php
/* 
 * Sendanor SimpleREST PHP Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi> 
 */

namespace SimpleREST\Request;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

/** REST Element */
abstract class Element extends Resource implements iElement {

}
