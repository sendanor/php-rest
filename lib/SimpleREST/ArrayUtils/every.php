<?php

namespace SimpleREST\ArrayUtils;

/**
 * @param array $array
 * @param callable $fn
 * @return bool
 */
function every (array $array, callable $fn) {

  foreach ($array as $value) {
    if (!$fn($value)) return false;
  }

  return true;

}
