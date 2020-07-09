<?php

namespace SimpleREST;

require_once( dirname(dirname(__FILE__)) . '/ArrayUtils/every.php');

use JsonSerializable;
use Exception;

class Decimal implements JsonSerializable {

  /**
   * @var int
   */
  const INTERNAL_SCALE = 10;

  /**
   * @var string
   */
  private $_value;

  /**
   * Decimal constructor.
   *
   * @param mixed $value
   */
  public function __construct ($value) {

    $this->_value = "" . $value;

  }

  /**
   * @return mixed
   */
  public function __toString () {
    return $this->_value;
  }

  /**
   * @return float
   */
  public function jsonSerialize () {
    return floatval( $this->_value );
  }

  /**
   * Returns the sum of arguments
   *
   * @param array $values
   * @return Decimal
   * @throws Exception if less than one argument
   */
  static public function sum (...$values) {

    if (count($values) < 0) {
      throw new Exception('Must have at least one argument!');
    }

    return new Decimal( array_reduce($values, function($total, $item) {
      return bcadd($total, "" . $item, self::INTERNAL_SCALE);
    } ) );

  }

  /**
   * Substract two or more values
   *
   * @param array $values
   * @return Decimal
   * @throws Exception if less than two argument
   */
  static public function sub (...$values) {

    if (count($values) <= 1) {
      throw new Exception('Must have at least two arguments!');
    }

    return new Decimal( array_reduce($values, function($a, $b) {
      return bcsub("".$a, "".$b, self::INTERNAL_SCALE);
    } ) );

  }

  /**
   * Divide two or more values
   *
   * @param array $values
   * @return Decimal
   * @throws Exception if one or less arguments
   * @throws Exception if divided by zero
   */
  static public function div (...$values) {

    if (count($values) <= 1) {
      throw new Exception('Must have at least two arguments!');
    }

    return new Decimal( array_reduce($values, function($a, $b) {

      $value = bcdiv("" . $a, "" . $b, self::INTERNAL_SCALE);

      if ($value === NULL) {
        throw new Exception('Divided by zero!');
      }

      return $value;

    } ) );

  }

  /**
   * Multiply two or more values
   *
   * @param array $values
   * @return Decimal
   * @throws Exception if one or less arguments
   */
  static public function mul (...$values) {

    if (count($values) <= 1) {
      throw new Exception('Must have at least two arguments!');
    }

    return new Decimal( array_reduce($values, function($a, $b) {

      return bcmul("" . $a, "" . $b, self::INTERNAL_SCALE);

    } ) );

  }

  /**
   * Compare two values.
   *
   * @param mixed $a
   * @param mixed $b
   * @return int
   */
  static public function compare ($a, $b) {
    return bccomp("" . $a, "" . $b, self::INTERNAL_SCALE);
  }

  /**
   * Returns TRUE if every argument is equal
   *
   * @param array $values
   * @return bool if every value in $value is equal or $values is empty
   */
  static public function isEqual (...$values) {

    if (count($values) === 0) return true;

    $first = array_shift($values);

    if (count($values) === 0) return true;

    return SimpleREST\ArrayUtils\every($values, function($item) use ($first) {
      return self::compare($first, $item) === 0;
    } );

  }

}