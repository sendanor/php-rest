<?php

namespace SimpleREST;

require_once( dirname(dirname(__FILE__)) . '/Log/index.php' );
require_once( dirname(dirname(__FILE__)) . '/ArrayUtils/every.php');
require_once( dirname(dirname(__FILE__)) . '/ArrayUtils/reduce.php');
require_once( dirname(dirname(__FILE__)) . '/Assert.class.php');

use SimpleREST\Log\Log;
use JsonSerializable;
use Exception;
use function SimpleREST\ArrayUtils\every;
use function SimpleREST\ArrayUtils\reduce;

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
   * @throws Exception if extension bcmath is not enabled
   */
  public function __construct ($value) {

    Assert::extensionLoaded("bcmath");

    $this->_value = "" . trim($value);

  }

  /**
   * @return bool
   */
  public function isNegative () {

    return substr($this->_value, 0, 1) === '-';

  }

  /**
   * @returns bool TRUE if this value has no digits after the dot,
   */
  public function isInteger () {

    return !self::_hasDecimalPoint( self::_removeLeadingZerosAfterDecimalPoint($this->_value) );

  }

  /**
   * @param Decimal $value
   * @throws Exception
   * @return bool TRUE if this value is greater than $value
   */
  public function isGreaterThan (Decimal $value) {

    return self::isGreater($this->_value, "" . $value);

  }

  /**
   * @param Decimal $value
   * @throws Exception
   * @return bool TRUE if this value is greater than $value
   */
  public function isLowerThan (Decimal $value) {

    return self::isLower($this->_value, "" . $value);

  }
  /**
   * @param Decimal $value
   * @throws Exception
   * @return bool TRUE if this value is greater than $value
   */
  public function isGreaterThanOrEqual (Decimal $value) {

    return self::isGreaterOrEqual($this->_value, "" . $value);

  }

  /**
   * @param Decimal $value
   * @throws Exception
   * @return bool TRUE if this value is greater than $value
   */
  public function isLowerThanOrEqual (Decimal $value) {

    return self::isLowerOrEqual($this->_value, "" . $value);

  }

  /**
   * @param string $value
   * @return bool
   */
  private static function _hasDecimalPoint (string $value) {
    return strpos($value, '.') !== FALSE;
  }

  /**
   * @param string $value
   * @return string
   */
  private static function _removeLeadingZerosAfterDecimalPoint (string $value) {

    if (!self::_hasDecimalPoint($value)) return $value;

    return rtrim(rtrim($value, '0'), '.');

  }

  /**
   * @param string $value
   * @return string
   */
  private static function _normalize (string $value) {

    return self::_removeLeadingZerosAfterDecimalPoint($value);

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
   * @throws Exception if bcmath extension is not loaded
   */
  static public function sum (...$values) {

    Assert::extensionLoaded("bcmath");

    if (count($values) < 0) {
      throw new Exception('Must have at least one argument!');
    }

    return new Decimal( reduce($values, function($a, $b) {

      return bcadd($a, "" . $b, self::INTERNAL_SCALE);

    } ) );

  }

  /**
   * Substract two or more values
   *
   * @param array $values
   * @return Decimal
   * @throws Exception if less than two argument
   * @throws Exception if bcmath extension is not loaded
   */
  static public function sub (...$values) {

    Assert::extensionLoaded("bcmath");

    if (count($values) <= 1) {
      throw new Exception('Must have at least two arguments!');
    }

    return new Decimal( reduce($values, function($a, $b) {
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
   * @throws Exception if bcmath extension is not loaded
   */
  static public function div (...$values) {

    Assert::extensionLoaded("bcmath");

    if (count($values) <= 1) {
      throw new Exception('Must have at least two arguments!');
    }

    return new Decimal( reduce($values, function($a, $b) {

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
   * @throws Exception if bcmath extension is not loaded
   */
  static public function mul (...$values) {

    Assert::extensionLoaded("bcmath");

    if (count($values) <= 1) {
      throw new Exception('Must have at least two arguments!');
    }

    return new Decimal( reduce($values, function($a, $b) {

      return bcmul("" . $a, "" . $b, self::INTERNAL_SCALE);

    } ) );

  }

  /**
   * Compare two values.
   *
   * @param mixed $a
   * @param mixed $b
   * @return int
   * @throws Exception if bcmath extension is not loaded
   */
  static public function compare ($a, $b) {

    Assert::extensionLoaded("bcmath");

    return (int) bccomp("" . $a, "" . $b, self::INTERNAL_SCALE);

  }

  /**
   * @param mixed $a
   * @param mixed $b
   * @return bool TRUE if $a is greater than $b
   * @throws Exception if bcmath extension is not loaded
   */
  static public function isGreater ($a, $b) {

    return self::compare($a, $b) === 1;

  }

  /**
   * @param mixed $a
   * @param mixed $b
   * @return bool TRUE if $a is greater than $b
   * @throws Exception if bcmath extension is not loaded
   */
  static public function isLower ($a, $b) {

    return self::compare($a, $b) === -1;

  }
  /**
   * @param mixed $a
   * @param mixed $b
   * @return bool TRUE if $a is greater than $b
   * @throws Exception if bcmath extension is not loaded
   */
  static public function isGreaterOrEqual ($a, $b) {

    return self::compare($a, $b) >= 0;

  }

  /**
   * @param mixed $a
   * @param mixed $b
   * @return bool TRUE if $a is greater than $b
   * @throws Exception if bcmath extension is not loaded
   */
  static public function isLowerOrEqual ($a, $b) {

    return self::compare($a, $b) <= 0;

  }

  /**
   * Returns TRUE if every argument is equal
   *
   * @param array $values
   * @return bool if every value in $value is equal or $values is empty
   * @throws Exception if bcmath extension is not loaded
   */
  static public function isEqual (...$values) {

    Assert::extensionLoaded("bcmath");

    if (count($values) === 0) return true;

    $first = array_shift($values);

    if (count($values) === 0) return true;

    return every($values, function($item) use ($first) {
      return self::compare($first, $item) === 0;
    } );

  }

  /**
   * @param Decimal $value
   * @return Decimal
   * @throws Exception if bcmath extension is not loaded
   */
  static public function floor (Decimal $value) {

    Assert::extensionLoaded("bcmath");

    if ($value->isInteger()) return new Decimal( self::_normalize('' . $value) );

    if ( $value->isNegative() ) {
      return new Decimal( self::_normalize(bcsub($value, 1, 0)) );
    }

    return new Decimal( self::_normalize(bcsub($value, 0, 0)) );

  }

  /**
   * @param Decimal $value
   * @return Decimal
   * @throws Exception if bcmath extension is not loaded
   */
  static public function ceil (Decimal $value) {

    Assert::extensionLoaded("bcmath");

    if ($value->isInteger()) return new Decimal( self::_normalize('' . $value) );

    if (!$value->isNegative()) {
      return new Decimal( self::_normalize(bcadd($value, 1, 0)) );
    }

    return new Decimal( self::_normalize(bcsub($value, 0, 0)) );

  }

  /**
   * @param Decimal $value
   * @return Decimal
   * @throws Exception if bcmath extension is not loaded
   */
  static public function abs (Decimal $value) {

    if ( !$value->isNegative() ) return $value;

    return new Decimal( ltrim( '' . $value, '-') );

  }

  /**
   * @param Decimal $value
   * @return Decimal
   * @throws Exception if bcmath extension is not loaded
   */
  static public function round (Decimal $value) {

    Assert::extensionLoaded("bcmath");

    if ($value->isInteger()) return new Decimal( self::_normalize('' . $value) );

    if ($value->isNegative()) {
      $decimals = self::sub($value, self::ceil($value));
    } else {
      $decimals = self::sub($value, self::floor($value));
    }

    if ($value->isNegative()) {

      if ( $decimals->isGreaterThanOrEqual( new Decimal('-0.5') ) ) {
        return self::ceil($value);
      }

      return self::floor($value);

    }

    if ( $decimals->isGreaterThanOrEqual( new Decimal('0.5') ) ) {
      return self::ceil($value);
    }

    return self::floor($value);

  }

  /**
   * @param Decimal $value
   * @param int $scale
   * @return string
   * @throws Exception if bcmath extension is not loaded
   */
  static public function format (Decimal $value, int $scale) {

    Assert::extensionLoaded("bcmath");

    return bcadd("".$value, 0, $scale);

  }

  /**
   * @return array
   */
  public function __debugInfo () {
    return [
      '_value' => $this->_value
    ];
  }

  /**
   * @param array $properties
   * @return Decimal
   * @throws Exception if extension bcmath is not loaded
   */
  public function __set_state (array $properties) {

    return new Decimal( $properties['_value'] );

  }

}