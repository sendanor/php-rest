<?php
declare(strict_types=1);

namespace SimpleREST;

require_once( dirname(__FILE__) . '/Log/functions.php');

use Exception;

class Assert {

  /**
   * @param mixed $value
   * @param mixed $class
   * @throws Exception if not a string
   */
  static public function instanceOf ($value, $class) {

    if ( !($value instanceof $class) ) {
      throw new Exception("Not instance of '$class': " . Log\stringifyValues($value) );
    }

  }

  /**
   * @param mixed $value
   * @param mixed $class
   * @throws Exception if not a string
   */
  static public function arrayItemsInstanceOf ($value, $class) {

    self::array($value);

    foreach ( $value as $key => $item ) {

      if ( !($item instanceof $class) ) {
        throw new Exception("Item(#$key) in array is not instance of '$class': " . Log\stringifyValues($item) );
      }

    }

  }

  /**
   * @param array $value
   * @throws Exception if not an array of strings
   */
  static public function arrayItemsAsString ($value) {

    self::array($value);

    foreach ( $value as $key => $item ) {

      if ( !is_string($item) ) {
        throw new Exception("Item(#$key) in array is not a string: " . Log\stringifyValues($item) );
      }

    }

  }

  /**
   * @param mixed $value
   * @throws Exception if not a string
   */
  static public function string ($value) {

    if (!is_string($value)) {
      throw new Exception("Not a string: " . Log\stringifyValues($value) );
    }

  }

  /**
   * @param mixed $value
   * @throws Exception if not a double
   */
  static public function double ($value) {

    if (!is_double($value)) {
      throw new Exception("Not a double: " . Log\stringifyValues($value) );
    }

  }

  /**
   * @param mixed $value
   * @throws Exception if not a scalar
   */
  static public function scalar ($value) {

    if (!is_scalar($value)) {
      throw new Exception("Not a scalar: " . Log\stringifyValues($value) );
    }

  }

  /**
   * @param mixed $value
   * @throws Exception if not a infinite
   */
  static public function infinite ($value) {

    if (!is_infinite($value)) {
      throw new Exception("Not a infinite: " . Log\stringifyValues($value) );
    }

  }

  /**
   * @param mixed $value
   * @throws Exception if not a array
   */
  static public function array ($value) {

    if (!is_array($value)) {
      throw new Exception("Not a array: " . Log\stringifyValues($value) );
    }

  }

  /**
   * @param mixed $value
   * @throws Exception if not a iterable
   */
  static public function iterable ($value) {

    if (!is_iterable($value)) {
      throw new Exception("Not a iterable: " . Log\stringifyValues($value) );
    }

  }

  /**
   * @param mixed $value
   * @throws Exception if not a nan
   */
  static public function nan ($value) {

    if (!is_nan($value)) {
      throw new Exception("Not a nan: " . Log\stringifyValues($value) );
    }

  }

  /**
   * @param mixed $value
   * @throws Exception if not a finite
   */
  static public function finite ($value) {

    if (!is_finite($value)) {
      throw new Exception("Not a finite: " . Log\stringifyValues($value) );
    }

  }

//  /**
//   * NOTE! This is only available in PHP 7.3 and up, we still support 7.2.
//   *
//   * @param mixed $value
//   * @throws Exception if not a countable
//   */
//  static public function countable ($value) {
//
//    if (!is_countable($value)) {
//      throw new Exception("Not a countable: " . Log\stringifyValues($value) );
//    }
//
//  }

  /**
   * @param mixed $value
   * @throws Exception if not a resource
   */
  static public function resource ($value) {

    if (!is_resource($value)) {
      throw new Exception("Not a resource: " . Log\stringifyValues($value) );
    }

  }

  /**
   * @param mixed $value
   * @throws Exception if not a object
   */
  static public function object ($value) {

    if (!is_object($value)) {
      throw new Exception("Not a object: " . Log\stringifyValues($value) );
    }

  }

  /**
   * @param mixed $value
   * @throws Exception if not a int
   */
  static public function int ($value) {

    if (!is_int($value)) {
      throw new Exception("Not a int: " . Log\stringifyValues($value) );
    }

  }

  /**
   * @param mixed $value
   * @throws Exception if not a bool
   */
  static public function bool ($value) {

    if (!is_bool($value)) {
      throw new Exception("Not a bool: " . Log\stringifyValues($value) );
    }

  }

  /**
   * @param mixed $value
   * @throws Exception if not a float
   */
  static public function float ($value) {

    if (!is_float($value)) {
      throw new Exception("Not a float: " . Log\stringifyValues($value) );
    }

  }

  /**
   * @param mixed $value
   * @throws Exception if not a numeric
   */
  static public function numeric ($value) {

    if (!is_numeric($value)) {
      throw new Exception("Not a numeric: " . Log\stringifyValues($value) );
    }

  }

  /**
   * @param mixed $value
   * @throws Exception if not a null
   */
  static public function null ($value) {

    if (!is_null($value)) {
      throw new Exception("Not a null: " . Log\stringifyValues($value) );
    }

  }

  /**
   * @param mixed $value
   * @throws Exception if not a callable
   */
  static public function callable ($value) {

    if (!is_callable($value)) {
      throw new Exception("Not a callable: " . Log\stringifyValues($value) );
    }

  }

  /**
   * Assert that this extension is loaded.
   *
   * @param string $name
   * @throws Exception
   */
  static public function extensionLoaded (string $name) {

    if (!extension_loaded($name)) {
      throw new Exception("Extension not loaded: " . $name );
    }

  }

}