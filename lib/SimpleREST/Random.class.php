<?php
declare(strict_types=1);

namespace SimpleREST;

use Exception;

/**
 * Static methods to generate cryptographically secure random content.
 *
 * @package SimpleREST
 */
class Random {

  /**
   * Generates cryptographically secure pseudo-random integers
   *
   * This is a wrapper for PHP `random_int()`.
   *
   * @param int $min The lowest value to be returned, which must be PHP_INT_MIN or higher.
   * @param int $max The highest value to be returned, which must be less than or equal to PHP_INT_MAX.
   * @return int a cryptographically secure random integer in the range min to max, inclusive.
   * @throws Exception if an appropriate source of randomness cannot be found
   */
  static public function int (int $min, int $max) {

    return random_int($min, $max);

  }

  /**
   * Generate cryptographically secure pseudo-random string.
   *
   * @param int $length The length of the string
   * @param string $chars The characters in the string
   * @return string The random string
   * @throws Exception if an appropriate source of randomness cannot be found
   */
  static public function string (int $length, string $chars = "0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM") {

    $x = '';

    for($i = 1; $i <= $length; $i++) {
      $x .= substr($chars, self::int(0, strlen($chars)-1 ), 1);
    }

    return $x;

  }

  /**
   * Generates cryptographically secure pseudo-random hex string.
   *
   * @param int $length The length of the string
   * @return string The random hex string
   * @throws Exception if an appropriate source of randomness cannot be found
   */
  static public function hex (int $length) {

    $x = '';

    for($i = 1; $i <= $length; $i++) {
      $x .= dechex( self::int(0, 15) );
    }

    return $x;

  }

}