<?php
/*
 * Sendanor's PHP REST Framework
 * Copyright 2017-2020 Jaakko-Heikki Heusala <jheusala@iki.fi>
 */

namespace SimpleREST\File;

use Exception;

/**
 * Implements lock file implementation
 *
 */
class ReadLock extends BaseLock {

  /**
   * ReadLock constructor.
   *
   * Create a lock file
   *
   * @param $file string
   * @throws Exception if cannot open lock file or file handle not found
   */
  public function __construct ( $file ) {

    parent::__construct( $file );

    $this->_openFileHandle();

    $this->_lockFileHandle(LOCK_SH);

  }

  /** Automatically releases the lock */
  public function __destruct () {

    parent::__destruct();

  }

  /**
   * Release lock if it is locked
   */
  public function release () {

    if ($this->_isFileHandleOpen()) {

      /** @noinspection PhpUnhandledExceptionInspection */
      $this->_unlockFileHandle();

      // We must clear cache for isFileHandleLinked()
      clearstatcache();

      // Try to obtain write lock to unlink the file
      /** @noinspection PhpUnhandledExceptionInspection */
      if ( $this->_isFileHandleLinked() && $this->_internalLockFileHandle(LOCK_EX|LOCK_NB) ) {

        // We must clear cache for lockFileExists() and isFileHandleLinked()
        clearstatcache();

        // Check if another process managed to get a lock while we were locking and removed the lock file...
        // Check if another process managed to create a new different lock file with same name...
        /** @noinspection PhpUnhandledExceptionInspection */
        if ( $this->_lockFileExists() && $this->_isFileHandleLinked() ) {

          $this->_removeLinkFile();

        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->_unlockFileHandle();

      }

    }

    $this->_closeFileHandle();

  }


  /**
   * Truncates the file handle and writes our PID in it
   *
   * @param string $operation string
   * @throws Exception
   */
  protected function _postLockFileHandle ( $operation ) {

    // We must clear cache for lockFileExists() and isFileHandleLinked()
    clearstatcache();

    // Check if another process managed to get a lock while we were waiting and removed the lock file...
    if ( !$this->_lockFileExists() ) {
      $this->_relockFileHandle($operation);
      return;
    }

    // Check if another process managed to create a new different lock file with same name...
    if ( !$this->_isFileHandleLinked() ) {
      $this->_relockFileHandle($operation);
      return;
    }

  }

}

