<?php
declare(strict_types=1);

namespace SimpleREST;

/**
 * Class ValidateUtils
 */
class Validate {

  /**
   * @param string $email
   * @return bool
   */
  static function isEmail (string $email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== FALSE;
  }

}