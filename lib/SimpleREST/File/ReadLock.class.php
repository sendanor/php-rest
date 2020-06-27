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

  /** Create a lock file */
  public function __construct ( $file ) {

    parent::__construct( $file );

    $this->openFileHandle();

    $this->lockFileHandle(LOCK_SH);

  }

  /** Automatically releases the lock */
  public function __destruct () {

    parent::__destruct();

  }

  /** Truncates the file handle and writes our PID in it */
  protected function postLockFileHandle ( $operation ) {

    // We must clear cache for lockFileExists() and isFileHandleLinked()
    clearstatcache();

    // Check if another process managed to get a lock while we were waiting and removed the lock file...
    if ( !$this->lockFileExists() ) {
      return $this->relockFileHandle($operation);
    }

    // Check if another process managed to create a new different lock file with same name...
    if ( !$this->isFileHandleLinked() ) {
      return $this->relockFileHandle($operation);
    }

  }

  /** Release lock if it is locked */
  public function release () {

    $this->unlockFileHandle();

    // We must clear cache for isFileHandleLinked()
    clearstatcache();

    // Try to obtain write lock to unlink the file
    if ( $this->isFileHandleOpen() && $this->isFileHandleLinked() && $this->internalLockFileHandle(LOCK_EX|LOCK_NB) ) {

      // We must clear cache for lockFileExists() and isFileHandleLinked()
      clearstatcache();

      // Check if another process managed to get a lock while we were locking and removed the lock file...
      // Check if another process managed to create a new different lock file with same name...
      if ( $this->lockFileExists() && $this->isFileHandleLinked() ) {

        $this->removeLinkFile();

      }

      $this->unlockFileHandle();

    }

    $this->closeFileHandle();

  }

}

