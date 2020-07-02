<?php

namespace SimpleREST;

use Exception;

/**
 * Class OutputBuffer
 */
class OutputBuffer {

  /**
   * If TRUE the buffering is still enabled.
   *
   * @var bool
   */
  private $_started;

  /**
   * OutputBuffer constructor.
   *
   * The callback will take signature: `function (string $buffer) : string` and it will be called when the buffer is closed.
   *
   * @param callable $fn
   */
  public function __construct (callable $fn) {

    $this->_started = true;

    ob_start($fn);

  }

  /**
   * The destructor will automatically close the buffer if not yet closed.
   */
  public function __destruct () {

    if ($this->_started) {
      /** @noinspection PhpUnhandledExceptionInspection */
      $this->close();
    }

  }

  /**
   * If TRUE, buffering is still started.
   *
   * @return bool
   */
  public function isOpen () {
    return $this->_started;
  }

  /**
   * If TRUE, buffering is still started.
   *
   * @return bool
   */
  public function isClosed () {
    return !$this->_started;
  }

  /**
   * @throws Exception if buffering has already been closed
   */
  public function close () {

    if (!$this->_started) {
      throw new Exception('Buffering has already been closed!');
    }

    $this->_started = false;

    ob_end_flush();

  }

}