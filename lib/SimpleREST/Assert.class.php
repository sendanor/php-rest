<?php

namespace SimpleREST;

use http\Exception\InvalidArgumentException;

class Assert {

  /**
   * @param mixed $value
   * @throws InvalidArgumentException if not a string
   */
  static public function string ($value) {

    if (!is_string($value)) {
      throw new InvalidArgumentException("Not a string: " . var_export($value, true) );
    }

  }

  /**
   * @param mixed $value
   * @throws InvalidArgumentException if not a double
   */
  static public function double ($value) {

    if (!is_double($value)) {
      throw new InvalidArgumentException("Not a double: " . var_export($value, true) );
    }

  }

  /**
   * @param mixed $value
   * @throws InvalidArgumentException if not a scalar
   */
  static public function scalar ($value) {

    if (!is_scalar($value)) {
      throw new InvalidArgumentException("Not a scalar: " . var_export($value, true) );
    }

  }

  /**
   * @param mixed $value
   * @throws InvalidArgumentException if not a infinite
   */
  static public function infinite ($value) {

    if (!is_infinite($value)) {
      throw new InvalidArgumentException("Not a infinite: " . var_export($value, true) );
    }

  }

  /**
   * @param mixed $value
   * @throws InvalidArgumentException if not a array
   */
  static public function array ($value) {

    if (!is_array($value)) {
      throw new InvalidArgumentException("Not a array: " . var_export($value, true) );
    }

  }

  /**
   * @param mixed $value
   * @throws InvalidArgumentException if not a iterable
   */
  static public function iterable ($value) {

    if (!is_iterable($value)) {
      throw new InvalidArgumentException("Not a iterable: " . var_export($value, true) );
    }

  }

  /**
   * @param mixed $value
   * @throws InvalidArgumentException if not a nan
   */
  static public function nan ($value) {

    if (!is_nan($value)) {
      throw new InvalidArgumentException("Not a nan: " . var_export($value, true) );
    }

  }

  /**
   * @param mixed $value
   * @throws InvalidArgumentException if not a finite
   */
  static public function finite ($value) {

    if (!is_finite($value)) {
      throw new InvalidArgumentException("Not a finite: " . var_export($value, true) );
    }

  }

//  /**
//   * NOTE! This is only available in PHP 7.3 and up, we still support 7.2.
//   *
//   * @param mixed $value
//   * @throws InvalidArgumentException if not a countable
//   */
//  static public function countable ($value) {
//
//    if (!is_countable($value)) {
//      throw new InvalidArgumentException("Not a countable: " . var_export($value, true) );
//    }
//
//  }

  /**
   * @param mixed $value
   * @throws InvalidArgumentException if not a resource
   */
  static public function resource ($value) {

    if (!is_resource($value)) {
      throw new InvalidArgumentException("Not a resource: " . var_export($value, true) );
    }

  }

  /**
   * @param mixed $value
   * @throws InvalidArgumentException if not a object
   */
  static public function object ($value) {

    if (!is_object($value)) {
      throw new InvalidArgumentException("Not a object: " . var_export($value, true) );
    }

  }

  /**
   * @param mixed $value
   * @throws InvalidArgumentException if not a int
   */
  static public function int ($value) {

    if (!is_int($value)) {
      throw new InvalidArgumentException("Not a int: " . var_export($value, true) );
    }

  }

  /**
   * @param mixed $value
   * @throws InvalidArgumentException if not a bool
   */
  static public function bool ($value) {

    if (!is_bool($value)) {
      throw new InvalidArgumentException("Not a bool: " . var_export($value, true) );
    }

  }

  /**
   * @param mixed $value
   * @throws InvalidArgumentException if not a float
   */
  static public function float ($value) {

    if (!is_float($value)) {
      throw new InvalidArgumentException("Not a float: " . var_export($value, true) );
    }

  }

  /**
   * @param mixed $value
   * @throws InvalidArgumentException if not a numeric
   */
  static public function numeric ($value) {

    if (!is_numeric($value)) {
      throw new InvalidArgumentException("Not a numeric: " . var_export($value, true) );
    }

  }

  /**
   * @param mixed $value
   * @throws InvalidArgumentException if not a null
   */
  static public function null ($value) {

    if (!is_null($value)) {
      throw new InvalidArgumentException("Not a null: " . var_export($value, true) );
    }

  }

  /**
   * @param mixed $value
   * @throws InvalidArgumentException if not a callable
   */
  static public function callable ($value) {

    if (!is_callable($value)) {
      throw new InvalidArgumentException("Not a callable: " . var_export($value, true) );
    }

  }

}