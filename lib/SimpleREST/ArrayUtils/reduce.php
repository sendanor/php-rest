<?php

namespace SimpleREST\ArrayUtils;

/**
 * Unlike PHP reduce, this will not call callback for the first NULL value.
 *
 * @param array $values
 * @param callable $fn
 * @return bool
 */
function reduce (array $values, callable $callback) {

  $first = true;

  return array_reduce($values, function($a, $b) use (&$first, $callback) {

    if ($first) {

      $first = false;

      if ($a === NULL) {
        return $b;
      }

    }

    return $callback($a, $b);

  });

}
