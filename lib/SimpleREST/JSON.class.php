<?php
declare(strict_types=1);

namespace SimpleREST;

use TypeError;

class JSON {

  /**
   * @param string $value
   * @return mixed
   */
  public static function decode (string $value) {

    $data = json_decode($value, true);

    if ($data === null) {

      $code = json_last_error();

      if ($code !== JSON_ERROR_NONE) {
        throw new TypeError('Could not encode JSON: ' . self::_stringifyJsonError($code));
      }

    }

    return $data;

  }

  /**
   * @param mixed $value
   * @return string
   * @throws TypeError if encoding fails
   */
  public static function encode ($value) {

    $data = json_encode($value);

    if ($data === FALSE) {
      throw new TypeError( 'Could not encode JSON: ' . self::_stringifyJsonError(json_last_error()) );
    }

    return $data;

  }

  /**
   * @param mixed $value
   * @return string
   * @throws TypeError if encoding fails
   */
  public static function encodeObject ($value) {

    $data = json_encode($value, JSON_FORCE_OBJECT);

    if ($data === FALSE) {
      throw new TypeError( 'Could not encode JSON: ' . self::_stringifyJsonError(json_last_error()) );
    }

    return $data;

  }

  /**
   * @param int $code
   * @return string
   * @throws TypeError if decoding fails
   */
  protected static function _stringifyJsonError (int $code) {

    switch ($code) {
      case JSON_ERROR_NONE:                 	return "No error has occurred";
      case JSON_ERROR_DEPTH: 	                return "The maximum stack depth has been exceeded";
      case JSON_ERROR_STATE_MISMATCH:        	return "Invalid or malformed JSON";
      case JSON_ERROR_CTRL_CHAR: 	            return "Control character error, possibly incorrectly encoded";
      case JSON_ERROR_SYNTAX: 	              return "Syntax error";
      case JSON_ERROR_UTF8:                 	return "Malformed UTF-8 characters, possibly incorrectly encoded";
      case JSON_ERROR_RECURSION: 	            return "One or more recursive references in the value to be encoded";
      case JSON_ERROR_INF_OR_NAN:           	return "One or more NAN or INF values in the value to be encoded";
      case JSON_ERROR_UNSUPPORTED_TYPE: 	    return "A value of a type that cannot be encoded was given";
      case JSON_ERROR_INVALID_PROPERTY_NAME: 	return "A property name that cannot be encoded was given";
      case JSON_ERROR_UTF16: 	                return "Malformed UTF-16 characters, possibly incorrectly encoded";
    }

    return 'Unknown error code (' . $code . ')';

  }

  /**
   * @param string $data
   * @return bool
   */
  public static function isJSONString (string $data) {
    json_decode($data);
    return json_last_error() === JSON_ERROR_NONE;
  }

  /**
   * @return string
   */
  public static function getLastErrorString () {
    return self::_stringifyJsonError(json_last_error());
  }

}