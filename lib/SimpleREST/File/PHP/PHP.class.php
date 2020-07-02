<?php
/*
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\File;

use Exception;
use Closure;
use Throwable;

/**
 * Implements readable accessor for text files in the filesystem.
 *
 */
class PHP {

  /**
   * The file to compile.
   *
   * @var string
   */
  protected $_file;

  /**
   * The file as a compiled function
   *
   * @var Closure|null
   */
  protected $_cb;

  /**
   * Result from the latest execution or null, if not executed.
   *
   * @var object|null
   */
  protected $_scope;

  /**
   * Text file reader constructor.
   *
   * @param string $file
   */
  public function __construct (string $file) {

    $this->_file = $file;
    $this->_cb = null;
    $this->_scope = null;

  }

  /**
   * Returns the compiled PHP file as a function.
   *
   * @param string $file
   * @return Closure
   */
  protected static function _compileAsFunction (string $file) {

    return function($options) use ($file) {

      $SCOPE = (object) [
        'file' => $file,
        'compiled' => false,
        'output' => null,
        'result' => null,
        'error' => null
      ];

      $output_cb = function($buffer) use (&$SCOPE) {
        $SCOPE->output = $buffer;
      };

      $sandbox_cb = function($OPTIONS) use (&$SCOPE) {

        extract($OPTIONS);

        /** @noinspection PhpIncludeInspection */
        return include $SCOPE->file;

      };

      ob_start($output_cb);

      try {

        $SCOPE->result = $sandbox_cb($options);

      } catch (Throwable $e) {

        $SCOPE->result = null;
        $SCOPE->error = $e;

      } finally {
        ob_end_flush();
      }

      return $SCOPE;

    };

  }

  /**
   * Compiles the PHP template file as an internal template closure function
   */
  public function compile () {

    $this->_cb = self::_compileAsFunction($this->_file);

  }

  /**
   * Executes the internal template function with optional $this.
   *
   * @param array $options Optional options
   * @return string
   * @throws Exception if template function does not return internal scope object
   */
  public function execute (array $options = array()) {

    if (!$this->isCompiled()) {
      $this->compile();
    }

    $this->_scope = ($this->_cb)($options);

    if ( $this->_scope === null ) {
      throw new Exception('Failed to execute template!');
    }

    if ( $this->_scope->error !== null ) {
      throw new $this->_scope->error;
    }

    return $this->_scope->output;

  }

  /**
   * TRUE if compiled template function has been called.
   *
   * @return mixed|null
   */
  public function isCalled () {
    return $this->_scope !== null;
  }

  /**
   * TRUE if file has been compiled as a function.
   *
   * @return mixed|null
   */
  public function isCompiled () {
    return $this->_cb !== null;
  }

  /**
   * Get result from previous compile.
   *
   * @return mixed|null
   */
  public function getScope () {
    return $this->_scope;
  }

  /**
   * Get result from previous compile.
   *
   * @return mixed|null
   */
  public function getResult () {
    return $this->_scope ? $this->_scope->result : null;
  }

  /**
   * Get output from previous compile.
   *
   * @return mixed|null
   */
  public function getOutput () {
    return $this->_scope ? $this->_scope->output : null;
  }

  /**
   * Returns the compiled PHP file as string.
   *
   * @return string
   */
  public function __toString () {

    return $this->getOutput();

  }

}
