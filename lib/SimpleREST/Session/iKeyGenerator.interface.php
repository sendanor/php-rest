<?php

declare(strict_types=1);

namespace SimpleREST\Session;

interface iKeyGenerator {

  /**
   * Generate a new session key
   *
   * @return string
   */
  public function createSessionKey () : string;

}
