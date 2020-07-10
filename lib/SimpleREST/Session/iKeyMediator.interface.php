<?php

declare(strict_types=1);

namespace SimpleREST\Session;

use Exception;

interface iKeyMediator {

  /**
   * Fetch previous key from the inventory.
   *
   * @return bool
   */
  public function hasKey () : bool;

  /**
   * Fetch previous key from the inventory.
   *
   * @return string
   * @throws Exception if key not found
   */
  public function getKey () : string;

  /**
   * Set a key to the inventory.
   *
   * @param string $key
   */
  public function setKey (string $key) : void;

}
