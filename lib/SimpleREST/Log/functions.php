<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\Log;

/**
 * @param $msg string
 */
function error ($msg) {
  Manager::getLogger()->error($msg);
}

/**
 * @param $msg string
 */
function warning ($msg) {
  Manager::getLogger()->warning($msg);
}

/**
 * @param $msg string
 */
function info ($msg) {
  Manager::getLogger()->info($msg);
}

/**
 * @param $logger
 */
function setLogger ($logger) {
  Manager::setLogger( $logger );
}
