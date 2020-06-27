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
class WriteLock extends BaseLock {

  /** Create a lock file */
  public function __construct ( $file ) {

    parent::__construct( $file );

    $this->openFileHandle();

    $this->lockFileHandle(LOCK_EX);

  }

  /** Automatically releases the lock */
  public function __destruct () {

    parent::__destruct();

  }

  /** Truncates the file handle and writes our PID in it */
  protected function postLockFileHandle () {

    // We must clear cache for lockFileExists() and isSameLockFile()
    clearstatcache();

    // Check if another process managed to get a lock while we were waiting and removed the lock file...
    if ( !$this->lockFileExists() ) {
      return $this->relockFileHandle($operation);
    }

    // Check if another process managed to create a new different lock file with same name...
    if ( !$this->isSameLockFile() ) {
      return $this->relockFileHandle($operation);
    }

    $this->truncateFileHandle();

    $this->writeToFileHandle( "" . getmypid() );

  }

  /** Release lock if it is locked */
  public function release () {

    $this->removeLinkFile();

    $this->unlockFileHandle();

    $this->closeFileHandle();

  }

  /** Truncate the file using file handle */
  protected function truncateFileHandle () {

    if ( ftruncate($this->getFileHandle(), 0) === FALSE ) {
      throw new Exception('Failed to truncate file handle');
    }

  }

  /** Write data to file handle */
  protected function writeToFileHandle (string $content) {

    if ( fwrite($this->getFileHandle(), $content ) == FALSE ) {
      throw new Exception('Failed to write to link file handle');
    }

  }

}

