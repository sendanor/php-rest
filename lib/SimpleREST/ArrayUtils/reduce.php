<?php

namespace SimpleREST\ArrayUtils;

/**
 * Unlike PHP reduce, this will not call callback for the first NULL value.
 *
 * @param array $values
 * @param callable $fn
 * @param mixed $initial
 * @return bool
 */
function reduce (array $values, callable $callback, $initial = NULL) {

  $first = true;

  $our_callback = function($a, $b) use (&$first, $callback) {

    if ($first) {

      $first = false;

      if ($a === NULL) {
        return $b;
      }

    }

    return $callback($a, $b);

  };

  if ($initial === NULL) {
    return array_reduce($values, $our_callback);
  }

  return array_reduce($values, $our_callback, $initial);

}
