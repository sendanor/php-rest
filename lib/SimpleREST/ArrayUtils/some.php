<?php
declare(strict_types=1);

namespace SimpleREST\ArrayUtils;

/**
 * @param array $array
 * @param callable $fn
 * @return bool
 */
function some (array $array, callable $fn) {

  foreach ($array as $value) {
    if ($fn($value)) return true;
  }

  return false;

}
